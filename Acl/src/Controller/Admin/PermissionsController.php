<?php

namespace Croogo\Acl\Controller\Admin;

use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Croogo\Core\Croogo;

/**
 * AclPermissions Controller
 *
 * @category Controller
 * @package  Croogo.Acl
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class PermissionsController extends AppController
{

    public function initialize()
    {
        parent::initialize();

        $this->loadModel('Croogo/Acl.Acos');
        $this->loadModel('Croogo/Acl.Aros');
        $this->loadModel('Croogo/Users.Roles');
        $this->loadModel('Croogo/Acl.Permissions');
    }

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        if ($this->request->action == 'toggle') {
            $this->Croogo->protectToggleAction();
        }
    }

/**
 * admin_index
 *
 * @param id integer aco id, when null, the root ACO is used
 * @return void
 */
    public function index($id = null, $level = null)
    {
        if (isset($this->request->query['root'])) {
            $query = strtolower($this->request->query('root'));
        }

        if ($id == null) {
            $root = isset($query) ? $query : 'controllers';
            $root = $this->Acos->node(str_replace('.', '_', $root));
            $root = $root->firstOrFail();
        } else {
            $root = $this->Acos->get($id);
        }

        if ($level !== null) {
            $level++;
        }

        $acos = [];
        $roles = $this->Roles->find('list');
        if ($root) {
            $acos = $this->Acos->getChildren($root->id);
        }
        $this->set(compact('acos', 'roles', 'level'));

        $aros = $this->Aros->getRoles($roles);
        if ($root && $this->RequestHandler->ext == 'json') {
            $options = array_intersect_key(
                $this->request->query,
                ['perms' => null, 'urls' => null]
            );
            $cacheName = 'permissions_aco_' . $root->id;
            $permissions = Cache::read($cacheName, 'permissions');
            if ($permissions === false) {
                $permissions = $this->Permissions->format($acos, $aros, $options);
                Cache::write($cacheName, $permissions, 'permissions');
            }
        } else {
            $permissions = [];
        }

        $this->set(compact('aros', 'permissions'));

        if ($this->request->is('ajax') && isset($query)) {
            $this->render('Croogo/Acl.acl_permissions_table');
        } else {
            $this->_setPermissionRoots();
        }
    }

    protected function _setPermissionRoots()
    {
        $roots = $this->Acos->getPermissionRoots();
        foreach ($roots as $id => $root) {
            Croogo::hookAdminTab(
                'Admin/Permissions/index',
                __d('croogo', $root->title),
                'Croogo/Core.blank',
                [
                    'linkOptions' => [
                        'data-alias' => $root->alias,
                    ],
                ]
            );
        }
        $this->set(compact('roots'));
    }

/**
 * toggle
 *
 * @param int $acoId
 * @param int $aroId
 * @return void
 */
    public function toggle($acoId, $aroId)
    {
        if (!$this->request->is('ajax')) {
            return $this->redirect(['action' => 'index']);
        }

        // see if acoId and aroId combination exists
        $aro = $this->Aros->get($aroId);
        $path = $this->Acos->find('path', ['for' => $acoId]);
        $path = join('/', collection($path)->extract('alias')->toArray());

        $permitted = !$this->Permissions->check(['model' => $aro->model, 'foreign_key' => $aro->foreign_key], $path);
        $success = $this->Permissions->allow(['model' => $aro->model, 'foreign_key' => $aro->foreign_key], $path, '*', $permitted ? 1 : -1);
        if ($success) {
            $aco = $this->Acos->get($acoId);
            $cacheName = 'permissions_aco_' . $aco->parent_id;
            Cache::delete($cacheName, 'permissions');
            Cache::delete('permissions_public', 'permissions');
        }

        $this->viewBuilder()->autoLayout(false);

        $this->set(compact('acoId', 'aroId', 'data', 'success', 'permitted'));
    }

}
