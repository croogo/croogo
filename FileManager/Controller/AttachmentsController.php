<?php

App::uses('FileManagerAppController', 'FileManager.Controller');

/**
 * Attachments Controller
 *
 * This file will take care of file uploads (with rich text editor integration).
 *
 * PHP version 5
 *
 * @category FileManager.Controller
 * @package  Croogo.FileManager.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AttachmentsController extends FileManagerAppController {

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array('FileManager.Attachment');

/**
 * Helpers used by the Controller
 *
 * @var array
 * @access public
 */
	public $helpers = array('FileManager.FileManager', 'Text', 'Croogo.Image');

/**
 * Provides backwards compatibility access to the deprecated properties
 */
	public function __get($name) {
		switch ($name) {
			case 'type':
			case 'uploadsDir':
				return $this->Attachment->{$name};
			break;
			default:
				return parent::__get($name);
		}
	}

/**
 * Provides backwards compatibility access for settings values to deprecated
 * properties
 */
	public function __set($name, $val) {
		switch ($name) {
			case 'type':
			case 'uploadsDir':
				return $this->Attachment->{$name} = $val;
			break;
			default:
				return parent::__set($name, $val);
		}
	}

/**
 * Before executing controller actions
 *
 * @return void
 * @access public
 */
	public function beforeFilter() {
		parent::beforeFilter();

		// Comment, Category, Tag not needed
		$this->Attachment->unbindModel(array(
			'hasMany' => array('Comment'),
			'hasAndBelongsToMany' => array('Category', 'Tag'))
		);

		$this->Attachment->type = $this->type;
		$this->Attachment->Behaviors->attach('Tree', array(
			'scope' => array(
				$this->Attachment->alias . '.type' => $this->type,
			)
		));
		$this->set('type', $this->Attachment->type);

		if ($this->action == 'admin_add') {
			$this->Security->csrfCheck = false;
		}
	}

/**
 * Admin index
 *
 * @return void
 * @access public
 */
	public function admin_index() {
		$this->set('title_for_layout', __d('croogo', 'Attachments'));

		$this->Attachment->recursive = 0;
		$this->paginate['Attachment']['order'] = 'Attachment.created DESC';
		$this->set('attachments', $this->paginate());
	}

/**
 * Admin add
 *
 * @return void
 * @access public
 */
	public function admin_add() {
		$this->set('title_for_layout', __d('croogo', 'Add Attachment'));

		if (isset($this->request->params['named']['editor'])) {
			$this->layout = 'admin_popup';
		}

		if ($this->request->is('post') || !empty($this->request->data)) {

			if (empty($this->data['Attachment'])) {
				$this->Attachment->invalidate('file', __d('croogo', 'Upload failed. Please ensure size does not exceed the server limit.'));
				return;
			}

			$this->Attachment->create();
			if ($this->Attachment->save($this->request->data)) {

				$this->Session->setFlash(__d('croogo', 'The Attachment has been saved'), 'default', array('class' => 'success'));

				if (isset($this->request->params['named']['editor'])) {
					$this->redirect(array('action' => 'browse'));
				} else {
					$this->redirect(array('action' => 'index'));
				}
			} else {
				$this->Session->setFlash(__d('croogo', 'The Attachment could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
	}

/**
 * Admin edit
 *
 * @param int $id
 * @return void
 * @access public
 */
	public function admin_edit($id = null) {
		$this->set('title_for_layout', __d('croogo', 'Edit Attachment'));

		if (isset($this->request->params['named']['editor'])) {
			$this->layout = 'admin_popup';
		}

		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Attachment'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Attachment->save($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The Attachment has been saved'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__d('croogo', 'The Attachment could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Attachment->read(null, $id);
		}
	}

/**
 * Admin delete
 *
 * @param int $id
 * @return void
 * @access public
 */
	public function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Attachment'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		if ($this->Attachment->delete($id)) {
			$this->Session->setFlash(__d('croogo', 'Attachment deleted'), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Attachment'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
	}

/**
 * Admin browse
 *
 * @return void
 * @access public
 */
	public function admin_browse() {
		$this->layout = 'admin_popup';
		$this->admin_index();
	}

}
