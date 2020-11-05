<?php
declare(strict_types=1);

namespace Croogo\Contacts\Controller;

use Cake\Core\Configure;
use Cake\Mailer\Email;
use Croogo\Contacts\Model\Entity\Contact;
use Croogo\Contacts\Model\Entity\Message;
use Croogo\Core\Croogo;
use Cake\Network\Exception\SocketException;

/**
 * Class ContactsController
 *
 * @property \Croogo\Contacts\Model\Table\ContactsTable $Contacts
 * @property \Croogo\Core\Controller\Component\CroogoComponent $Croogo
 * @property \Croogo\Meta\Controller\Component\MetaComponent $Meta
 * @property \Croogo\Blocks\Controller\Component\BlocksComponent $BlocksHook
 * @property \Croogo\Acl\Controller\Component\FilterComponent $Filter
 * @property \Acl\Controller\Component\AclComponent $Acl
 * @property \Croogo\Core\Controller\Component\ThemeComponent $Theme
 * @property \Croogo\Acl\Controller\Component\AccessComponent $Access
 * @property \Croogo\Settings\Controller\Component\SettingsComponent $SettingsComponent
 * @property \Croogo\Nodes\Controller\Component\NodesComponent $NodesHook
 * @property \Croogo\Menus\Controller\Component\MenuComponent $Menu
 * @property \Croogo\Users\Controller\Component\LoggedInUserComponent $LoggedInUser
 * @property \Croogo\Taxonomy\Controller\Component\TaxonomyComponent $Taxonomy
 * @property \Croogo\Core\Controller\Component\AkismetComponent $Akismet
 * @property \Croogo\Core\Controller\Component\RecaptchaComponent $Recaptcha
 */
class ContactsController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();

        $this->_loadCroogoComponents([
            'Akismet',
            'Recaptcha' => [
                'actions' => ['view']
            ]
        ]);
    }

    /**
     * View
     *
     * @param string $alias
     * @return \Cake\Http\Response|void
     * @throws NotFoundException
     */
    public function view($alias = null)
    {
        if (!$alias) {
            $alias = 'contact';
        }
        $contact = $this->Contacts->find()
            ->where([
                'alias' => $alias,
                'status' => 1,
            ])
            ->firstOrFail();

        $continue = true;
        if (!$contact->message_status) {
            $continue = false;
        }
        $message = $this->Contacts->Messages->newEntity([]);
        if ($this->getRequest()->is('post') && $continue === true) {
            $message = $this->Contacts->Messages->patchEntity($message, $this->getRequest()->getData());
            $message->contact_id = $contact->id;
            Croogo::dispatchEvent('Controller.Contacts.beforeMessage', $this);

            $continue = $this->_spamProtection($continue, $contact, $message);
            $continue = $this->_captcha($continue, $contact, $message);
            $continue = $this->_validation($continue, $contact, $message);
            $continue = $this->_sendEmail($continue, $contact, $message);
            $this->set(compact('continue'));

            if ($continue === true) {
                $this->Contacts->Messages->save($message);
                Croogo::dispatchEvent('Controller.Contacts.afterMessage', $this);
                $this->Flash->success(__d('croogo', 'Your message has been received...'));

                return $this->redirect($this->referer());
            }
        }

        $this->Croogo->viewFallback([
            'view_' . $contact->id,
            'view_' . $contact->alias,
        ]);
        $this->set('contact', $contact);
        $this->set('message', $message);
    }

    /**
     * Validation
     *
     * @param bool $continue
     * @param array $contact
     * @return bool
     * @access protected
     */
    protected function _validation($continue, Contact $contact, Message $message)
    {
        if ($message->getErrors() || $continue === false) {
            return false;
        }

        if ($contact->message_archive && !$this->Contacts->Messages->save($message)) {
            return false;
        }

        return $continue;
    }

    /**
     * Spam protection
     *
     * @param bool $continue
     * @param \Croogo\Contacts\Model\Entity\Contact $contact
     * @return bool
     * @access protected
     */
    protected function _spamProtection($continue, Contact $contact, Message $message)
    {
        if (!$contact->message_spam_protection || $continue === false) {
            return $continue;
        }
        $this->Akismet->setCommentAuthor($message->name);
        $this->Akismet->setCommentAuthorEmail($message->email);
        $this->Akismet->setCommentContent($message->body);
        if ($this->Akismet->isCommentSpam()) {
            $this->Flash->error(__d('croogo', 'Sorry, the message appears to be spam.'));

            return false;
        }

        return true;
    }

    /**
     * Captcha
     *
     * @param bool $continue
     * @param array $contact
     * @return bool
     * @access protected
     */
    protected function _captcha($continue, Contact $contact, Message $message)
    {
        if (!$contact->message_captcha || $continue === false) {
            return $continue;
        }

        if (!$this->Recaptcha->verify()) {
            $this->Flash->error(__d('croogo', 'Invalid captcha entry'));

            return false;
        }

        return true;
    }

    /**
     * Send Email
     *
     * @param bool $continue
     * @param array $contact
     * @return bool
     * @access protected
     */
    protected function _sendEmail($continue, Contact $contact, Message $message)
    {
        if (!$contact->message_notify || $continue === false) {
            return $continue;
        }

        $email = new Email();
        $siteTitle = Configure::read('Site.title');
        try {
            $email->setFrom($message->email)
                ->setTo($contact->email)
                ->setSubject(__d('croogo', '[%s] %s', $siteTitle, $contact->title))
                ->setViewVars([
                    'contact' => $contact,
                    'message' => $message,
                ])
                ->viewBuilder()->setTemplate('Croogo/Contacts.contact');
            if ($this->viewBuilder()->getTheme()) {
                $email->viewBuilder()->setTheme($this->viewBuilder()->getTheme());
            }
            if (!$email->send()) {
                $continue = false;
            }
        } catch (SocketException $e) {
            $this->log(sprintf('Error sending contact notification: %s', $e->getMessage()));
            $continue = false;
        }

        return $continue;
    }
}
