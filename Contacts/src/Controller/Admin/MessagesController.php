<?php

namespace Croogo\Contacts\Controller\Admin;

use Croogo\Contacts\Controller\Admin\Controller;

/**
 * Messages Controller
 *
 * @category Contacts.Controller
 * @package  Croogo.Contacts.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MessagesController extends Controller {

/**
 * Components
 *
 * @var array
 * @access public
 */
	public $components = array(
		'Croogo/Core.BulkProcess',
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
	public function index() {
		$this->set('title_for_layout', __d('croogo', 'Messages'));
		$this->Prg->commonProcess();

		$query = $this->Messages->find('searchable', $this->Prg->parsedParams());
		$messages = $this->paginate($query);
		$contacts = $this->Messages->Contacts->find('list');
		$searchFields = array('contact_id', 'status' => array(
			'label' => __d('croogo', 'Read'),
			'type' => 'hidden',
		));
		$this->set(compact('messages', 'contacts', 'searchFields'));
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function edit($id = null) {
		$this->set('title_for_layout', __d('croogo', 'Edit Message'));

		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Message'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Message->save($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The Message has been saved'), 'flash', array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__d('croogo', 'The Message could not be saved. Please, try again.'), 'flash', array('class' => 'error'));
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
	public function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Message'), 'flash', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}
		if ($this->Message->delete($id)) {
			$this->Session->setFlash(__d('croogo', 'Message deleted'), 'flash', array('class' => 'success'));
			return $this->redirect(array('action' => 'index'));
		}
	}

/**
 * Admin process
 *
 * @return void
 * @access public
 */
	public function process() {
		$Message = $this->{$this->modelClass};
		list($action, $ids) = $this->BulkProcess->getRequestVars($Message->alias);

		$messageMap = array(
			'delete' => __d('croogo', 'Messages deleted'),
			'read' => __d('croogo', 'Messages marked as read'),
			'unread' => __d('croogo', 'Messages marked as unread'),
		);
		return $this->BulkProcess->process($Message, $action, $ids, $messageMap);
	}

}
