<?php

namespace Croogo\Menus\Controller\Admin;

use Cake\Core\Configure;
use Cake\Event\Event;
use Croogo\Core\Controller\Component\CroogoComponent;
use Croogo\Core\Controller\CroogoAppController;
use Croogo\Core\Croogo;
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

		$this->loadComponent('Croogo/Core.BulkProcess');
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
 */
	public function add($menuId = null) {
		$this->set('title_for_layout', __d('croogo', 'Add Link'));

		$link = $this->Links->newEntity([
			'menu_id' => $menuId
		]);

		$menus = $this->Links->Menus->find('list');
		$menu = $this->Links->Menus->get($link->menu_id);
		$roles = $this->Roles->find('list');
		$parentLinks = $this->Links->find('treeList', [
			'Links.menu_id' => $menuId,
		]);
		$this->set(compact('link', 'menu', 'menus', 'roles', 'parentLinks', 'menuId'));

		if (!$this->request->is('post')) {
			return;
		}



		$link = $this->Links->patchEntity($link, $this->request->data);
		$this->Links->setTreeScope($link->menu_id);
		$link = $this->Links->save($link);
		if (!$link) {
			$this->Flash->error(__d('croogo', 'The Link could not be saved. Please, try again.'));

			return;
		}

		$this->Flash->success(__d('croogo', 'The Link has been saved'));

		if (isset($this->request->data['apply'])) {
			return $this->redirect(array('action' => 'edit', $link->id));
		} else {
			return $this->redirect(array(
				'action' => 'index',
				'?' => array(
					'menu_id' => $menuId
				)
			));
		}

	}

/**
 * Admin edit
 *
 * @param integer $id
 */
	public function edit($id = null) {
		$this->set('title_for_layout', __d('croogo', 'Edit Link'));

		$link = $this->Links->get($id);

		$menus = $this->Links->Menus->find('list');
		$roles = $this->Roles->find('list');
		$menu = $this->Links->Menus->get($link->menu_id);
		$parentLinks = $this->Links->find('treeList', [
			'Link.menu_id' => $menu->id,
		]);
		$menuId = $menu->id;
		$this->set(compact('link', 'menu', 'menus', 'roles', 'parentLinks', 'menuId'));

		if (!$this->request->is('put')) {
			return;
		}

		$link = $this->Links->patchEntity($link, $this->request->data);
		if (!$this->Links->save($link)) {
			$this->Flash->error(__d('croogo', 'The Link could not be saved. Please, try again.'));

			return;
		}

		$this->Flash->success(__d('croogo', 'The Link has been saved'));
		if (isset($this->request->data['apply'])) {
			return $this->redirect(array('action' => 'edit', $id));
		} else {
			return $this->redirect(array(
				'action' => 'index',
				'?' => array(
					'menu_id' => $menu->id
				)
			));
		}
	}

/**
 * Admin delete
 *
 * @param integer $id
 */
	public function delete($id = null) {
		$link = $this->Links->get($id);

		$this->Links->setTreeScope($link->menu_id);
		if (!$this->Links->delete($link)) {
			return;
		}

		$this->Flash->success(__d('croogo', 'Link deleted'));
		return $this->redirect(array(
			'action' => 'index',
			'?' => array(
				'menu_id' => $link->menu_id,
			),
		));
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

}
