<?php
/**
 * Contacts Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ContactsController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    var $name = 'Contacts';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    var $uses = array('Contact');

    function admin_index() {
        $this->pageTitle = __('Contacts', true);

        $this->Contact->recursive = 0;
        $this->paginate['Contact']['order'] = 'Contact.title ASC';
        $this->set('contacts', $this->paginate());
    }

    function admin_add() {
        $this->pageTitle = __("Add Contact", true);

        if (!empty($this->data)) {
            $this->Contact->create();
            if ($this->Contact->save($this->data)) {
                $this->Session->setFlash(__('The Contact has been saved', true));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Contact could not be saved. Please, try again.', true));
            }
        }
    }

    function admin_edit($id = null) {
        $this->pageTitle = __("Edit Contact", true);

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Contact', true));
            $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->data)) {
            if ($this->Contact->save($this->data)) {
                $this->Session->setFlash(__('The Contact has been saved', true));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Contact could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Contact->read(null, $id);
        }
    }

    function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Contact', true));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->Contact->delete($id)) {
            $this->Session->setFlash(__('Contact deleted', true));
            $this->redirect(array('action'=>'index'));
        }
    }

    function view($alias = null) {
        if (!$alias) {
            $this->redirect('/');
        }

        $contact = $this->Contact->find('first', array(
            'Contact.alias' => $alias,
            'Contact.status' => 1,
        ));
        if (!isset($contact['Contact']['id'])) {
            $this->redirect('/');
        }
        $this->set('contact', $contact);

        $continue = true;
        if (!$contact['Contact']['message_status']) {
            $continue = false;
        }
        if (!empty($this->data) &&
            $continue === true) {
            $this->data['Message']['contact_id'] = $contact['Contact']['id'];
            $continue = $this->__validation($continue, $contact);
            $continue = $this->__spam_protection($continue, $contact);
            $continue = $this->__captcha($continue, $contact);
            $continue = $this->__send_email($continue, $contact);

            if ($continue === true) {
                //$this->Session->setFlash(__('Your message has been received.', true));
                //unset($this->data['Message']);

                $this->flash(__('Your message has been received...', true), '/');
            }
        }

        $this->pageTitle = $contact['Contact']['title'];
    }

    function __validation($continue, $contact) {
        if ($this->Contact->Message->set($this->data) &&
            $this->Contact->Message->validates() &&
            $continue === true) {
            if ($contact['Contact']['message_archive'] &&
                !$this->Contact->Message->save($this->data['Message'])) {
                $continue = false;
            }
        } else {
            $continue = false;
        }

        return $continue;
    }

    function __spam_protection($continue, $contact) {
        if (!empty($this->data) &&
            $contact['Contact']['message_spam_protection'] &&
            $continue === true) {
            $this->Akismet->setCommentAuthor($this->data['Message']['name']);
            $this->Akismet->setCommentAuthorEmail($this->data['Message']['email']);
            $this->Akismet->setCommentContent($this->data['Comment']['body']);
            if ($this->Akismet->isCommentSpam()) {
                $continue = false;
                $this->Session->setFlash(__('Sorry, the message appears to be spam.', true));
            }
        }

        return $continue;
    }

    function __captcha($continue, $contact) {
        if (!empty($this->data) &&
            $contact['Contact']['message_captcha'] &&
            $continue === true &&
            !$this->Recaptcha->valid($this->params['form'])) {
            $continue = false;
            $this->Session->setFlash(__('Invalid captcha entry', true));
        }

        return $continue;
    }

    function __send_email($continue, $contact) {
        if ($contact['Contact']['message_notify'] &&
            $continue === true) {
            $this->Email->to = $contact['Contact']['email'];
            $this->Email->from = $this->data['Message']['name'] . ' <' . $this->data['Message']['email'] . '>';
            $this->Email->subject = '[' . Configure::read('Site.title') . '] ' . $contact['Contact']['title'];
            $this->Email->template = 'contact';

            $this->set('contact', $contact);
            $this->set('message', $this->data);
            $this->Email->send();
        }

        return $continue;
    }

}
?>