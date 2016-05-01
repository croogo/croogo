<?php

namespace Croogo\Menus\Controller\Admin;

use Cake\Cache\Cache;
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
class LinksController extends AppController
{
	public function initialize()
    {
		parent::initialize();

		$this->loadComponent('Croogo/Core.BulkProcess');
		$this->loadModel('Croogo/Users.Roles');
	}
    /**
     * Admin delete
     *
     * @param int $id
     *
     * @return \Cake\Network\Response|null|void
     */
    public function delete($id = null)
    {
        $link = $this->Links->get($id);

        $this->Links->setTreeScope($link->menu_id);
        if (!$this->Links->delete($link)) {
            return;
        }

        $this->Flash->success(__d('croogo', 'Link deleted'));
        return $this->redirect([
            'action' => 'index',
            '?' => [
                'menu_id' => $link->menu_id,
            ],
        ]);
    }

    /**
     * Admin moveup
     *
     * @param int $id
     * @param int $step
     *
     * @return \Cake\Network\Response|null
     */
    public function admin_moveup($id, $step = 1)
    {
        $link = $this->Link->findById($id);
        if (!isset($link['Link']['id'])) {
            $this->Flash->error(__d('croogo', 'Invalid id for Link'));
            return $this->redirect([
                'controller' => 'menus',
                'action' => 'index',
            ]);
        }
        $this->Link->setTreeScope($link['Link']['menu_id']);
        if ($this->Link->moveUp($id, $step)) {
            Cache::clearGroup('menus', 'croogo_menus');
            $this->Flash->success(__d('croogo', 'Moved up successfully'));
        } else {
            $this->Flash->error(__d('croogo', 'Could not move up'));
        }
        return $this->redirect([
            'action' => 'index',
            '?' => [
                'menu_id' => $link['Link']['menu_id'],
            ],
        ]);
    }

    /**
     * Admin movedown
     *
     * @param int $id
     * @param int $step
     *
     * @return \Cake\Network\Response|null
     */
    public function admin_movedown($id, $step = 1)
    {
        $link = $this->Link->findById($id);
        if (!isset($link['Link']['id'])) {
            $this->Flash->error(__d('croogo', 'Invalid id for Link'));
            return $this->redirect([
                'controller' => 'menus',
                'action' => 'index',
            ]);
        }
        $this->Link->setTreeScope($link['Link']['menu_id']);
        if ($this->Link->moveDown($id, $step)) {
            Cache::clearGroup('menus', 'croogo_menus');
            $this->Flash->success(__d('croogo', 'Moved down successfully'));
        } else {
            $this->Flash->error(__d('croogo', 'Could not move down'));
        }
        return $this->redirect([
            'action' => 'index',
            '?' => [
                'menu_id' => $link['Link']['menu_id'],
            ],
        ]);
    }

    /**
     * Admin process
     *
     * @param int $menuId
     *
     * @return null
     */
    public function process($menuId = null)
    {
        $Links = $this->Links;
        list($action, $ids) = $this->BulkProcess->getRequestVars($Links->alias());

        $redirect = ['action' => 'index'];

        $menu = $this->Links->Menus->get($menuId);
        if (isset($menu->id)) {
            $redirect['?'] = ['menu_id' => $menuId];
        }
        $this->Links->setTreeScope($menuId);

        $multiple = ['copy' => false];
        $messageMap = [
            'delete' => __d('croogo', 'Links deleted'),
            'publish' => __d('croogo', 'Links published'),
            'unpublish' => __d('croogo', 'Links unpublished'),
        ];
        $options = compact('multiple', 'redirect', 'messageMap');
        return $this->BulkProcess->process($this->Links, $action, $ids, $options);
    }

    public function beforeCrudRender(Event $event)
    {
        $menuId = null;
        if (isset($event->subject()->entity)) {
            $menuId = $event->subject()->entity->menu_id;
        }
        if ($this->request->query('menu_id')) {
            $menuId = $this->request->query('menu_id');
        }
        if (!$menuId) {
            return;
        }

        $menu = $this->Links->Menus->get($menuId);

        $this->set('menu', $menu);
        $this->set('roles', $this->Roles->find('list'));
        $this->set('parentLinks', $this->Links->find('treeList')->where([
            'menu_id' => $menu->id,
        ]));
    }

    public function beforeCrudRedirect(Event $event)
    {
        $entity = $event->subject()->entity;

        $event->subject()->url['menu_id'] = $entity->menu_id;
    }

    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.beforeRender' => 'beforeCrudRender',
            'Crud.beforeRedirect' => 'beforeCrudRedirect'
        ];
    }
}
