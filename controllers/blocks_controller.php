<?php
/**
 * Blocks Controller
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
class BlocksController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'Blocks';

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array('Block', 'Role');

	public function admin_index() {
		$this->set('title_for_layout', __('Blocks', true));

		$this->Block->recursive = 0;
		$this->paginate['Block']['order'] = array('Block.weight' => 'ASC');
		$this->set('blocks', $this->paginate());
	}

	public function admin_add() {
		$this->set('title_for_layout', __('Add Block', true));

		if (!empty($this->data)) {
			$this->Block->create();
			$this->data['Block']['visibility_roles'] = $this->Block->encodeData($this->data['Role']['Role']);
			$this->data['Block']['visibility_paths'] = $this->Block->encodeData(explode("\n", $this->data['Block']['visibility_paths']));
			if ($this->Block->save($this->data)) {
				$this->Session->setFlash(__('The Block has been saved', true), 'default', array('class' => 'success'));
				if (isset($this->params['form']['apply'])) {
					$this->redirect(array('action'=>'edit', $this->Block->id));
				} else {
					$this->redirect(array('action'=>'index'));
				}
			} else {
				$this->Session->setFlash(__('The Block could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
			}
		}
		$regions = $this->Block->Region->find('list');
		$roles   = $this->Role->find('list');
		$this->set(compact('regions', 'roles'));
	}

	public function admin_edit($id = null) {
		$this->set('title_for_layout', __('Edit Block', true));

		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Block', true), 'default', array('class' => 'error'));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			$this->data['Block']['visibility_roles'] = $this->Block->encodeData($this->data['Role']['Role']);
			$this->data['Block']['visibility_paths'] = $this->Block->encodeData(explode("\n", $this->data['Block']['visibility_paths']));
			if ($this->Block->save($this->data)) {
				$this->Session->setFlash(__('The Block has been saved', true), 'default', array('class' => 'success'));
				if (! isset($this->params['form']['apply'])) {
					$this->redirect(array('action'=>'index'));
				}
			} else {
				$this->Session->setFlash(__('The Block could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->data)) {
			$data = $this->Block->read(null, $id);
			$data['Role']['Role'] = $this->Block->decodeData($data['Block']['visibility_roles']);
			if ($data['Block']['visibility_paths'] != '') {
				$data['Block']['visibility_paths'] = implode("\n", $this->Block->decodeData($data['Block']['visibility_paths']));
			}
			$this->data = $data;
		}
		$regions = $this->Block->Region->find('list');
		$roles = $this->Role->find('list');
		$this->set(compact('regions', 'roles'));
	}

	public function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Block', true), 'default', array('class' => 'error'));
			$this->redirect(array('action'=>'index'));
		}
		if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
			$blackHoleCallback = $this->Security->blackHoleCallback;
			$this->$blackHoleCallback();
		}
		if ($this->Block->delete($id)) {
			$this->Session->setFlash(__('Block deleted', true), 'default', array('class' => 'success'));
			$this->redirect(array('action'=>'index'));
		}
	}

	public function admin_moveup($id, $step = 1) {
		if( $this->Block->moveup($id, $step) ) {
			$this->Session->setFlash(__('Moved up successfully', true), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('Could not move up', true), 'default', array('class' => 'error'));
		}

		$this->redirect(array('action' => 'index'));
	}

	public function admin_movedown($id, $step = 1) {
		if( $this->Block->movedown($id, $step) ) {
			$this->Session->setFlash(__('Moved down successfully', true), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('Could not move down', true), 'default', array('class' => 'error'));
		}

		$this->redirect(array('action' => 'index'));
	}

	public function admin_process() {
		$action = $this->data['Block']['action'];
		$ids = array();
		foreach ($this->data['Block'] AS $id => $value) {
			if ($id != 'action' && $value['id'] == 1) {
				$ids[] = $id;
			}
		}

		if (count($ids) == 0 || $action == null) {
			$this->Session->setFlash(__('No items selected.', true), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		if ($action == 'delete' &&
			$this->Block->deleteAll(array('Block.id' => $ids), true, true)) {
			$this->Session->setFlash(__('Blocks deleted.', true), 'default', array('class' => 'success'));
		} elseif ($action == 'publish' &&
			$this->Block->updateAll(array('Block.status' => 1), array('Block.id' => $ids))) {
			$this->Session->setFlash(__('Blocks published', true), 'default', array('class' => 'success'));
		} elseif ($action == 'unpublish' &&
			$this->Block->updateAll(array('Block.status' => 0), array('Block.id' => $ids))) {
			$this->Session->setFlash(__('Blocks unpublished', true), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('An error occurred.', true), 'default', array('class' => 'error'));
		}

		$this->redirect(array('action' => 'index'));
	}

}
