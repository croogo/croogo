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

	public function admin_index() {
		$this->set('title_for_layout', __('Messages', true));

		$this->Message->recursive = 0;
		$this->paginate['Message']['conditions'] = array('Message.status' => 0);
		if (isset($this->params['named']['contact'])) {
			$this->paginate['Message']['conditions'] = $this->params['named']['contact'];
		}

		if (isset($this->params['named']['filter'])) {
			$filters = $this->Croogo->extractFilter();
			foreach ($filters AS $filterKey => $filterValue) {
				if (strpos($filterKey, '.') === false) {
					$filterKey = 'Message.' . $filterKey;
				}
				$this->paginate['Message']['conditions'][$filterKey] = $filterValue;
			}
		}

		if ($this->paginate['Message']['conditions']['Message.status'] == 1) {
			$this->set('title_for_layout', __('Messages: Read', true));
		} else {
			$this->set('title_for_layout', __('Messages: Unread', true));
		}

		$this->paginate['Message']['order'] = 'Message.title ASC';
		$this->set('messages', $this->paginate());
	}

	public function admin_edit($id = null) {
		$this->set('title_for_layout', __('Edit Message', true));

		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Message', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Message->save($this->data)) {
				$this->Session->setFlash(__('The Message has been saved', true), 'default', array('class' => 'success'));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Message could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Message->read(null, $id);
		}
	}

	public function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Message', true), 'default', array('class' => 'error'));
			$this->redirect(array('action'=>'index'));
		}
		if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
			$blackHoleCallback = $this->Security->blackHoleCallback;
			$this->$blackHoleCallback();
		}
		if ($this->Message->delete($id)) {
			$this->Session->setFlash(__('Message deleted', true), 'default', array('class' => 'success'));
			$this->redirect(array('action'=>'index'));
		}
	}

	public function admin_process() {
		$action = $this->data['Message']['action'];
		$ids = array();
		foreach ($this->data['Message'] AS $id => $value) {
			if ($id != 'action' && $value['id'] == 1) {
				$ids[] = $id;
			}
		}

		if (count($ids) == 0 || $action == null) {
			$this->Session->setFlash(__('No items selected.', true), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		if ($action == 'delete' &&
			$this->Message->deleteAll(array('Message.id' => $ids), true, true)) {
			$this->Session->setFlash(__('Messages deleted.', true), 'default', array('class' => 'success'));
		} elseif ($action == 'read' &&
			$this->Message->updateAll(array('Message.status' => 1), array('Message.id' => $ids))) {
			$this->Session->setFlash(__('Messages marked as read', true), 'default', array('class' => 'success'));
		} elseif ($action == 'unread' &&
			$this->Message->updateAll(array('Message.status' => 0), array('Message.id' => $ids))) {
			$this->Session->setFlash(__('Messages marked as unread', true), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('An error occurred.', true), 'default', array('class' => 'error'));
		}

		$this->redirect(array('action' => 'index'));
	}

}
