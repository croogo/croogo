<?php
/**
 * Messages Controller
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
class MessagesController extends AppController {

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
	public $uses = array('Message');

/**
 * Admin index
 *
 * @return void
 * @access public
 */
	public function admin_index() {
		$this->set('title_for_layout', __('Messages'));

		$this->Message->recursive = 0;
		$this->paginate['Message']['conditions'] = array('Message.status' => 0);
		if (isset($this->request->params['named']['contact'])) {
			$this->paginate['Message']['conditions'] = $this->request->params['named']['contact'];
		}

		if (isset($this->request->params['named']['filter'])) {
			$filters = $this->Croogo->extractFilter();
			foreach ($filters as $filterKey => $filterValue) {
				if (strpos($filterKey, '.') === false) {
					$filterKey = 'Message.' . $filterKey;
				}
				$this->paginate['Message']['conditions'][$filterKey] = $filterValue;
			}
		}

		if ($this->paginate['Message']['conditions']['Message.status'] == 1) {
			$this->set('title_for_layout', __('Messages: Read'));
		} else {
			$this->set('title_for_layout', __('Messages: Unread'));
		}

		$this->paginate['Message']['order'] = 'Message.title ASC';
		$this->set('messages', $this->paginate());
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_edit($id = null) {
		$this->set('title_for_layout', __('Edit Message'));

		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid Message'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Message->save($this->request->data)) {
				$this->Session->setFlash(__('The Message has been saved'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Message could not be saved. Please, try again.'), 'default', array('class' => 'error'));
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
			$this->Session->setFlash(__('Invalid id for Message'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Message->delete($id)) {
			$this->Session->setFlash(__('Message deleted'), 'default', array('class' => 'success'));
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
			$this->Session->setFlash(__('No items selected.'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		if ($action == 'delete' &&
			$this->Message->deleteAll(array('Message.id' => $ids), true, true)) {
			$this->Session->setFlash(__('Messages deleted.'), 'default', array('class' => 'success'));
		} elseif ($action == 'read' &&
			$this->Message->updateAll(array('Message.status' => 1), array('Message.id' => $ids))) {
			$this->Session->setFlash(__('Messages marked as read'), 'default', array('class' => 'success'));
		} elseif ($action == 'unread' &&
			$this->Message->updateAll(array('Message.status' => 0), array('Message.id' => $ids))) {
			$this->Session->setFlash(__('Messages marked as unread'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('An error occurred.'), 'default', array('class' => 'error'));
		}

		$this->redirect(array('action' => 'index'));
	}

}
