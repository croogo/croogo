<?php

App::uses('ContactsAppController', 'Contacts.Controller');

/**
 * Messages Controller
 *
 * PHP version 5
 *
 * @category Contacts.Controller
 * @package  Croogo.Contacts.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MessagesController extends ContactsAppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'Messages';

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array('Contacts.Message');

/**
 * Components
 *
 * @var array
 * @access public
 */
	public $components = array(
		'Search.Prg' => array(
			'presetForm' => array(
				'paramType' => 'querystring',
			),
			'commonProcess' => array(
				'paramType' => 'querystring',
				'filterEmpty' => true,
			),
		),
	);

/**
 * Preset Search Variables
 */
	public $presetVars = true;

/**
 * Admin index
 *
 * @return void
 * @access public
 */
	public function admin_index() {
		$this->set('title_for_layout', __d('croogo', 'Messages'));
		$this->Prg->commonProcess();

		$this->Message->recursive = 0;
		$criteria = $this->Message->parseCriteria($this->request->query);
		$contacts = $this->Message->Contact->find('list');
		$messages = $this->paginate($criteria);
		$searchFields = array('contact_id', 'status' => array(
			'label' => __d('croogo', 'Read'),
			'type' => 'hidden',
		));
		$this->set(compact('criteria', 'messages', 'contacts', 'searchFields'));
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_edit($id = null) {
		$this->set('title_for_layout', __d('croogo', 'Edit Message'));

		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Message'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Message->save($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The Message has been saved'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__d('croogo', 'The Message could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Message->read(null, $id);
		}
	}

/**
 * Admin delete
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Message'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Message->delete($id)) {
			$this->Session->setFlash(__d('croogo', 'Message deleted'), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}
	}

/**
 * Admin process
 *
 * @return void
 * @access public
 */
	public function admin_process() {
		$action = $this->request->data['Message']['action'];
		$ids = array();
		foreach ($this->request->data['Message'] as $id => $value) {
			if ($id != 'action' && $value['id'] == 1) {
				$ids[] = $id;
			}
		}

		if (count($ids) == 0 || $action == null) {
			$this->Session->setFlash(__d('croogo', 'No items selected.'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		if ($action == 'delete' &&
			$this->Message->deleteAll(array('Message.id' => $ids), true, true)) {
			$this->Session->setFlash(__d('croogo', 'Messages deleted.'), 'default', array('class' => 'success'));
		} elseif ($action == 'read' &&
			$this->Message->updateAll(array('Message.status' => 1), array('Message.id' => $ids))) {
			$this->Session->setFlash(__d('croogo', 'Messages marked as read'), 'default', array('class' => 'success'));
		} elseif ($action == 'unread' &&
			$this->Message->updateAll(array('Message.status' => 0), array('Message.id' => $ids))) {
			$this->Session->setFlash(__d('croogo', 'Messages marked as unread'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'An error occurred.'), 'default', array('class' => 'error'));
		}

		$this->redirect(array('action' => 'index'));
	}

}
