<?php

namespace Croogo\Menus\Controller\Admin;

use Cake\Event\Event;
use Croogo\Croogo\Controller\Component\CroogoComponent;
use Croogo\Croogo\Controller\CroogoAppController;
use Croogo\Menus\Controller\MenusAppController;
use Croogo\Menus\Model\Table\LinksTable;

/**
 * Links Controller
 *
 * @property CroogoComponent Croogo
 * @property LinksTable Links
 * @category Controller
 * @package  Croogo.Menus.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class LinksController extends CroogoAppController {

/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	public function beforeFilter(Event $event) {
		parent::beforeFilter($event);

//		$this->Security->unlockedActions[] = 'admin_toggle';
	}

	public function initialize() {
		parent::initialize();

		$this->loadComponent('Croogo/Croogo.BulkProcess');
		$this->loadModel('Croogo/Users.Roles');
	}


	/**
 * Toggle Link status
 *
 * @param $id string Link id
 * @param $status integer Current Link status
 * @return void
 */
	public function toggle($id = null, $status = null) {
		$this->Croogo->fieldToggle($this->Links, $id, $status);
	}

/**
 * Admin index
 */
	public function index() {
		$menuId = $this->request->query('menu_id');

		$menu = $this->Links->Menus->get($menuId);

		$this->set('title_for_layout', __d('croogo', 'Links: %s', $menu->title));

		$linksTree = $this->Links->find('treeList')->where([
			'Links.menu_id' => $menuId,
		]);
		$linksStatus = $this->Links->find('list', [
			'valueField' => 'status',
		])->where([
			'Links.menu_id' => $menuId,
		])->toArray();
		$this->set(compact('linksTree', 'linksStatus', 'menu'));
		$this->set('_serialize', ['linksTree', 'menu', 'linksStatus']);
	}

/**
 * Admin add
 *
 * @param integer $menuId
 * @return void
 * @access public
 */
	public function admin_add($menuId = null) {
		$this->set('title_for_layout', __d('croogo', 'Add Link'));

		if (!empty($this->request->data)) {
			$this->Link->create();
			$this->request->data['Link']['visibility_roles'] = $this->Link->encodeData($this->request->data['Role']['Role']);
			$this->Link->setTreeScope($this->request->data['Link']['menu_id']);
			if ($this->Link->save($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The Link has been saved'), 'default', array('class' => 'success'));
				if (isset($this->request->data['apply'])) {
					return $this->redirect(array('action' => 'edit', $this->Link->id));
				} else {
					return $this->redirect(array(
						'action' => 'index',
						'?' => array(
							'menu_id' => $this->request->data['Link']['menu_id']
						)
					));
				}
			} else {
				$this->Session->setFlash(__d('croogo', 'The Link could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
		$menus = $this->Link->Menu->find('list');
		$roles = $this->Role->find('list');
		$parentLinks = $this->Link->generateTreeList(array(
			'Link.menu_id' => $menuId,
		));
		$this->set(compact('menus', 'roles', 'parentLinks', 'menuId'));
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_edit($id = null) {
		$this->set('title_for_layout', __d('croogo', 'Edit Link'));

		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Link'), 'default', array('class' => 'error'));
			return $this->redirect(array(
				'controller' => 'menus',
				'action' => 'index',
			));
		}
		if (!empty($this->request->data)) {
			$this->request->data['Link']['visibility_roles'] = $this->Link->encodeData($this->request->data['Role']['Role']);

			if ($this->Link->save($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The Link has been saved'), 'default', array('class' => 'success'));
				if (isset($this->request->data['apply'])) {
					return $this->redirect(array('action' => 'edit', $this->Link->id));
				} else {
					return $this->redirect(array(
						'action' => 'index',
						'?' => array(
							'menu_id' => $this->request->data['Link']['menu_id']
						)
					));
				}
			} else {
				$this->Session->setFlash(__d('croogo', 'The Link could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->request->data)) {
			$data = $this->Link->read(null, $id);
			$data['Role']['Role'] = $this->Link->decodeData($data['Link']['visibility_roles']);
			$this->request->data = $data;
		}
		$menus = $this->Link->Menu->find('list');
		$roles = $this->Role->find('list');
		$menu = $this->Link->Menu->findById($this->request->data['Link']['menu_id']);
		$parentLinks = $this->Link->generateTreeList(array(
			'Link.menu_id' => $menu['Menu']['id'],
		));
		$menuId = $menu['Menu']['id'];
		$this->set(compact('menus', 'roles', 'parentLinks', 'menuId'));
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
			$this->Session->setFlash(__d('croogo', 'Invalid id for Link'), 'default', array('class' => 'error'));
			return $this->redirect(array(
				'controller' => 'menus',
				'action' => 'index',
			));
		}
		$link = $this->Link->findById($id);
		if (!isset($link['Link']['id'])) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Link'), 'default', array('class' => 'error'));
			return $this->redirect(array(
				'controller' => 'menus',
				'action' => 'index',
			));
		}
		$this->Link->setTreeScope($link['Link']['menu_id']);
		if ($this->Link->delete($id)) {
			$this->Session->setFlash(__d('croogo', 'Link deleted'), 'default', array('class' => 'success'));
			return $this->redirect(array(
				'action' => 'index',
				'?' => array(
					'menu_id' => $link['Link']['menu_id'],
				),
			));
		}
	}

/**
 * Admin moveup
 *
 * @param integer $id
 * @param integer $step
 * @return void
 * @access public
 */
	public function admin_moveup($id, $step = 1) {
		$link = $this->Link->findById($id);
		if (!isset($link['Link']['id'])) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Link'), 'default', array('class' => 'error'));
			return $this->redirect(array(
				'controller' => 'menus',
				'action' => 'index',
			));
		}
		$this->Link->setTreeScope($link['Link']['menu_id']);
		if ($this->Link->moveUp($id, $step)) {
			Cache::clearGroup('menus','croogo_menus');
			$this->Session->setFlash(__d('croogo', 'Moved up successfully'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Could not move up'), 'default', array('class' => 'error'));
		}
		return $this->redirect(array(
			'action' => 'index',
			'?' => array(
				'menu_id' => $link['Link']['menu_id'],
			),
		));
	}

/**
 * Admin movedown
 *
 * @param integer $id
 * @param integer $step
 * @return void
 * @access public
 */
	public function admin_movedown($id, $step = 1) {
		$link = $this->Link->findById($id);
		if (!isset($link['Link']['id'])) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Link'), 'default', array('class' => 'error'));
			return $this->redirect(array(
				'controller' => 'menus',
				'action' => 'index',
			));
		}
		$this->Link->setTreeScope($link['Link']['menu_id']);
		if ($this->Link->moveDown($id, $step)) {
			Cache::clearGroup('menus','croogo_menus');
			$this->Session->setFlash(__d('croogo', 'Moved down successfully'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Could not move down'), 'default', array('class' => 'error'));
		}
		return $this->redirect(array(
			'action' => 'index',
			'?' => array(
				'menu_id' => $link['Link']['menu_id'],
			),
		));
	}

/**
 * Admin process
 *
 * @param integer $menuId
 * @return void
 * @access public
 */
	public function admin_process($menuId = null) {
		$Link = $this->{$this->modelClass};
		list($action, $ids) = $this->BulkProcess->getRequestVars($Link->alias);

		$redirect = array('action' => 'index');

		$menu = $this->Link->Menu->findById($menuId);
		if (isset($menu['Menu']['id'])) {
			$redirect['?'] = array('menu_id' => $menuId);
		}
		$this->Link->setTreeScope($menuId);

		$multiple = array('copy' => false);
		$messageMap = array(
			'delete' => __d('croogo', 'Links deleted'),
			'publish' => __d('croogo', 'Links published'),
			'unpublish' => __d('croogo', 'Links unpublished'),
		);
		$options = compact('multiple', 'redirect', 'messageMap');
		return $this->BulkProcess->process($this->Link, $action, $ids, $options);

	}

	public function admin_link_chooser() {
		Croogo::dispatchEvent('Controller.Links.setupLinkChooser', $this);
		$linkChoosers = Configure::read('Menus.linkChoosers');
		$this->set(compact('linkChoosers'));
	}

}
