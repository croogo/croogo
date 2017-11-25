<?php

namespace Croogo\Contacts\Controller;

use Cake\Core\Configure;
use Cake\Mailer\Email;
use Croogo\Contacts\Model\Entity\Message;
use Croogo\Core\Croogo;

/**
 * Class ContactsController
 */
class ContactsController extends AppController
{

    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Croogo/Core.Recaptcha', [
            'actions' => ['view']
        ]);
    }

    /**
     * View
     *
     * @param string $alias
     * @return void
     * @access public
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
        $message = $this->Contacts->Messages->newEntity();
        if ($this->request->is('post') && $continue === true) {
            $this->Contacts->Messages->patchEntity($message, $this->request->data);
            $message->contact_id = $contact->id;
            Croogo::dispatchEvent('Controller.Contacts.beforeMessage', $this);

            $continue = $this->_spamProtection($continue, $contact, $message);
            $continue = $this->_captcha($continue, $contact, $message);
            $continue = $this->_validation($continue, $contact, $message);
            $continue = $this->_sendEmail($continue, $contact, $message);
            $this->set(compact('continue'));

            if ($continue === true) {
                Croogo::dispatchEvent('Controller.Contacts.afterMessage', $this);
                $this->Flash->success(__d('croogo', 'Your message has been received...'));

                return $this->Croogo->redirect('/');
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
     * @param boolean $continue
     * @param array $contact
     * @return boolean
     * @access protected
     */
    protected function _validation($continue, $contact, Message $message)
    {
        if ($message->errors() || $continue === false) {
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
     * @param boolean $continue
     * @param array $contact
     * @return boolean
     * @access protected
     */
    protected function _spamProtection($continue, $contact, Message $message)
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
     * @param boolean $continue
     * @param array $contact
     * @return boolean
     * @access protected
     */
    protected function _captcha($continue, $contact, Message $message)
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
     * @param boolean $continue
     * @param array $contact
     * @return boolean
     * @access protected
     */
    protected function _sendEmail($continue, $contact, Message $message)
    {
        if (!$contact->message_notify || $continue === false) {
            return $continue;
        }

        $email = new Email();
        $siteTitle = Configure::read('Site.title');
        try {
            $email->from($message->email)
                ->to($contact->email)
                ->subject(__d('croogo', '[%s] %s', $siteTitle, $contact->title))
                ->template('Croogo/Contacts.contact')
                ->viewVars([
                    'contact' => $contact,
                    'message' => $message,
                ]);
            if ($this->viewBuilder()->theme()) {
                $email->theme($this->viewBuilder()->theme());
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
