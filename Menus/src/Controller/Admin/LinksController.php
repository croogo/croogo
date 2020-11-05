<?php
declare(strict_types=1);

namespace Croogo\Menus\Controller\Admin;

use Cake\Cache\Cache;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Croogo\Core\Controller\Component\CroogoComponent;
use Croogo\Core\Controller\CroogoAppController;
use Croogo\Menus\Controller\MenusAppController;
use Croogo\Menus\Model\Table\LinksTable;

/**
 * Links Controller
 *
 * @property \Croogo\Core\Controller\Component\CroogoComponent $Croogo
 * @property \Croogo\Menus\Model\Table\LinksTable $Links
 * @category Controller
 * @package  Croogo.Menus.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 * @property \Croogo\Meta\Controller\Component\MetaComponent $Meta
 * @property \Croogo\Blocks\Controller\Component\BlocksComponent $BlocksHook
 * @property \Croogo\Acl\Controller\Component\FilterComponent $Filter
 * @property \Acl\Controller\Component\AclComponent $Acl
 * @property \Croogo\Core\Controller\Component\ThemeComponent $Theme
 * @property \Croogo\Acl\Controller\Component\AccessComponent $Access
 * @property \Croogo\Settings\Controller\Component\SettingsComponent $SettingsComponent
 * @property \Croogo\Nodes\Controller\Component\NodesComponent $NodesHook
 * @property \Croogo\Menus\Controller\Component\MenuComponent $Menu
 * @property \Croogo\Users\Controller\Component\LoggedInUserComponent $LoggedInUser
 * @property \Croogo\Taxonomy\Controller\Component\TaxonomyComponent $Taxonomy
 * @property \Crud\Controller\Component\CrudComponent $Crud
 * @property \Croogo\Core\Controller\Component\BulkProcessComponent $BulkProcess
 */
class LinksController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Croogo/Core.BulkProcess');
        $this->loadModel('Croogo/Users.Roles');

        if ($this->getRequest()->getParam('action') == 'toggle') {
            $this->Croogo->protectToggleAction();
        }
    }

    public function index()
    {
        $menuId = $this->getRequest()->getQuery('menu_id');
        $menu = $this->Links->Menus->get($menuId);
        $this->set('title_for_layout', __d('croogo', 'Links: %s', $menu->title));
        $linksTree = $this->Links->find('treeList')
            ->where([
                'Links.menu_id' => $menuId,
            ]);
        $linksStatus = $this->Links->find('list', [
            'valueField' => 'status',
        ])
            ->where([
                'Links.menu_id' => $menuId,
            ])
            ->toArray();
        $this->set(compact('linksTree', 'linksStatus', 'menu'));
        $this->set('_serialize', ['linksTree', 'menu', 'linksStatus']);
    }

    /**
     * Admin delete
     *
     * @param int $id
     *
     * @return \Cake\Network\Response|null|void
     */
    public function delete($id)
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
    public function moveUp($id, $step = 1)
    {
        try {
            $link = $this->Links->get($id);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__d('croogo', 'Invalid id for Link'));

            return $this->redirect([
                'controller' => 'Menus',
                'action' => 'index',
            ]);
        }

        $this->Links->setTreeScope($link->menu_id);
        if ($this->Links->moveUp($link, $step)) {
            Cache::clearGroup('menus', 'croogo_menus');
            $this->Flash->success(__d('croogo', 'Moved up successfully'));
        } else {
            $this->Flash->error(__d('croogo', 'Could not move up'));
        }

        return $this->redirect([
            'action' => 'index',
            '?' => [
                'menu_id' => $link->menu_id,
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
    public function moveDown($id, $step = 1)
    {
        try {
            $link = $this->Links->get($id);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__d('croogo', 'Invalid id for Link'));

            return $this->redirect([
                'controller' => 'Menus',
                'action' => 'index',
            ]);
        }

        $this->Links->setTreeScope($link->menu_id);
        if ($this->Links->moveDown($link, $step)) {
            Cache::clearGroup('menus', 'croogo_menus');
            $this->Flash->success(__d('croogo', 'Moved down successfully'));
        } else {
            $this->Flash->error(__d('croogo', 'Could not move down'));
        }

        return $this->redirect([
            'action' => 'index',
            '?' => [
                'menu_id' => $link->menu_id,
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
        list($action, $ids) = $this->BulkProcess->getRequestVars($Links->getAlias());

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
        $conditions = [];
        if (isset($event->getSubject()->entity) && $event->getSubject()->entity->isNew() === false) {
            $menuId = $event->getSubject()->entity->menu_id;
            $conditions[$this->Links->aliasField('id') . ' !='] = $event->getSubject()->entity->id;
        }
        if ($this->getRequest()->getQuery('menu_id')) {
            $menuId = $this->getRequest()->getQuery('menu_id');
        }
        if (!$menuId) {
            return;
        }

        $menu = $this->Links->Menus->get($menuId);
        $conditions['menu_id'] = $menu->id;

        $this->set('menu', $menu);
        $this->set('roles', $this->Roles->find('list'));
        $this->set('parentLinks', $this->Links->find('treeList')->where($conditions));
    }

    public function beforeCrudRedirect(Event $event)
    {
        if ($this->redirectToSelf($event)) {
            return;
        }

        $entity = $event->getSubject()->entity;
        $event->getSubject()->url['?'] = ['menu_id' => $entity->menu_id];
    }

    public function implementedEvents(): array
    {
        return parent::implementedEvents() + [
            'Crud.beforeRender' => 'beforeCrudRender',
            'Crud.beforeRedirect' => 'beforeCrudRedirect',
        ];
    }
}
