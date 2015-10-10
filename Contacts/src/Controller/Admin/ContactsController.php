<?php

namespace Croogo\Contacts\Controller\Admin;

use App\Network\Email\Email;
use Croogo\Contacts\Model\Entity\Contact;

/**
 * Contacts Controller
 *
 * @category Controller
 * @package  Croogo.Contacts.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ContactsController extends AppController {

/**
 * Components
 *
 * @var array
 * @access public
 */
	public $components = array(
		'Croogo/Core.Akismet',
		'Croogo/Core.Recaptcha',
	);

/**
 * Admin index
 *
 * @return void
 * @access public
 */
	public function index() {
		$this->set('title_for_layout', __d('croogo', 'Contacts'));

		$this->paginate = [
			'order' => [
				'title' => 'ASC'
			]
		];
		$this->set('contacts', $this->paginate());
		$this->set('displayFields', $this->Contacts->displayFields());
	}

/**
 * Admin add
 *
 * @return void
 * @access public
 */
	public function add() {
		$this->set('title_for_layout', __d('croogo', 'Add Contact'));

		$contact = $this->Contacts->newEntity();
		if (!empty($this->request->data)) {
			$contact = $this->Contacts->patchEntity($contact, $this->request->data);
			$contact = $this->Contacts->save($contact);
			if ($contact) {
				$this->Flash->success(__d('croogo', 'The Contact has been saved'));
				return $this->Croogo->redirect(array('action' => 'edit', $contact->id));
			} else {
				$this->Flash->error(__d('croogo', 'The Contact could not be saved. Please, try again.'));
			}
		}
		$this->set(compact('contact'));
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function edit($id = null) {
		$this->set('title_for_layout', __d('croogo', 'Edit Contact'));

		if (!$id && empty($this->request->data)) {
			$this->Flash->error(__d('croogo', 'Invalid Contact'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			$contact = $this->Contacts->get($id);
			$contact = $this->Contacts->newEntity($this->request->data);
			if ($this->Contacts->save($contact)) {
				$this->Flash->success(__d('croogo', 'The Contact has been saved'));
				return $this->Croogo->redirect(array('action' => 'edit', $id));
			} else {
				$this->Flash->error(__d('croogo', 'The Contact could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$contact = $this->Contacts->get($id);
			$this->set(compact('contact'));
		}
	}

/**
 * Admin delete
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function delete($id = null) {
		if (!$id) {
			$this->Flash->error(__d('croogo', 'Invalid id for Contact'));
			return $this->redirect(array('action' => 'index'));
		}
		$contact = new Contact(['id' => $id], ['markNew' => false]);
		if ($this->Contacts->delete($contact)) {
			$this->Flash->success(__d('croogo', 'Contact deleted'));
			return $this->redirect(array('action' => 'index'));
		}
	}

/**
 * View
 *
 * @param string $alias
 * @return void
 * @access public
 * @throws NotFoundException
 */
	public function view($alias = null) {
		if (!$alias) {
			$alias = 'contact';
		}

		$contact = $this->Contact->find('first', array(
			'conditions' => array(
				'Contact.alias' => $alias,
				'Contact.status' => 1,
			),
			'cache' => array(
				'name' => $alias,
				'config' => 'contacts_view',
			),
		));
		if (!isset($contact['Contact']['id'])) {
			throw new NotFoundException();
		}
		$this->set('contact', $contact);

		$continue = true;
		if (!$contact['Contact']['message_status']) {
			$continue = false;
		}
		if (!empty($this->request->data) && $continue === true) {
			$this->request->data['Message']['contact_id'] = $contact['Contact']['id'];
			$this->request->data['Message']['title'] = htmlspecialchars($this->request->data['Message']['title']);
			$this->request->data['Message']['name'] = htmlspecialchars($this->request->data['Message']['name']);
			$this->request->data['Message']['body'] = htmlspecialchars($this->request->data['Message']['body']);
			Croogo::dispatchEvent('Controller.Contacts.beforeMessage', $this);
			$continue = $this->_spam_protection($continue, $contact);
			$continue = $this->_captcha($continue, $contact);
			$continue = $this->_validation($continue, $contact);
			$continue = $this->_send_email($continue, $contact);

			$this->set(compact('continue'));
			if ($continue === true) {
				Croogo::dispatchEvent('Controller.Contacts.afterMessage', $this);
				$this->Session->setFlash(__d('croogo', 'Your message has been received...'), 'flash', array('class' => 'success'));
				return $this->Croogo->redirect('/');
			}
		} else {
			$this->Croogo->setReferer();
		}

		$this->Croogo->viewFallback(array(
			'view_' . $contact['Contact']['id'],
			'view_' . $contact['Contact']['alias'],
		));
		$this->set('title_for_layout', $contact['Contact']['title']);
	}

/**
 * Validation
 *
 * @param boolean $continue
 * @param array $contact
 * @return boolean
 * @access protected
 */
	protected function _validation($continue, $contact) {
		if ($this->Contacts->Message->set($this->request->data) &&
			$this->Contacts->Message->validates() &&
			$continue === true) {
			if ($contact['Contact']['message_archive'] &&
				!$this->Contacts->Message->save($this->request->data['Message'])) {
				$continue = false;
			}
		} else {
			$continue = false;
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
	protected function _spam_protection($continue, $contact) {
		if (!empty($this->request->data) &&
			$contact['Contact']['message_spam_protection'] &&
			$continue === true) {
			$this->Akismet->setCommentAuthor($this->request->data['Message']['name']);
			$this->Akismet->setCommentAuthorEmail($this->request->data['Message']['email']);
			$this->Akismet->setCommentContent($this->request->data['Message']['body']);
			if ($this->Akismet->isCommentSpam()) {
				$continue = false;
				$this->Session->setFlash(__d('croogo', 'Sorry, the message appears to be spam.'), 'flash', array('class' => 'error'));
			}
		}

		return $continue;
	}

/**
 * Captcha
 *
 * @param boolean $continue
 * @param array $contact
 * @return boolean
 * @access protected
 */
	protected function _captcha($continue, $contact) {
		if (!empty($this->request->data) &&
			$contact['Contact']['message_captcha'] &&
			$continue === true &&
			!$this->Recaptcha->valid($this->request)) {
			$continue = false;
			$this->Session->setFlash(__d('croogo', 'Invalid captcha entry'), 'flash', array('class' => 'error'));
		}

		return $continue;
	}

/**
 * Send Email
 *
 * @param boolean $continue
 * @param array $contact
 * @return boolean
 * @access protected
 */
	protected function _send_email($continue, $contact) {
		$email = new Email();
		if (!$contact['Contact']['message_notify'] || $continue !== true) {
			return $continue;
		}

		$siteTitle = Configure::read('Site.title');
		try {
			$email->from($this->request->data['Message']['email'])
				->to($contact['Contact']['email'])
				->subject(__d('croogo', '[%s] %s', $siteTitle, $contact['Contact']['title']))
				->template('Contacts.contact')
				->viewVars(array(
					'contact' => $contact,
					'message' => $this->request->data,
				));
			if ($this->theme) {
				$email->theme($this->theme);
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
