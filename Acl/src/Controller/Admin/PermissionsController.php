<?php

namespace Croogo\Acl\Controller\Admin;

use Cake\Cache\Cache;
use Cake\Event\Event;
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

    /**
     * Most leaves a permissions search lists; the rest is only counted
     */
    private const SEARCH_LIMIT = 100;

    public function initialize(): void
    {
        parent::initialize();

        $this->Acos = $this->fetchTable('Croogo/Acl.Acos');
        $this->Aros = $this->fetchTable('Croogo/Acl.Aros');
        $this->Roles = $this->fetchTable('Croogo/Users.Roles');
        $this->Permissions = $this->fetchTable('Croogo/Acl.Permissions');
    }

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(\Cake\Event\EventInterface $event): void
    {
        parent::beforeFilter($event);
        if ($this->getRequest()->getParam('action') == 'toggle') {
            $this->Croogo->protectToggleAction();
        }
    }

    /**
     * admin_index
     *
     * @param id integer aco id, when null, the root ACO is used
     * @return void
     */
    public function index($id = null, $level = null): void
    {
        // Cake 4: RequestHandler->ext (rozszerzenie URL) usunięte; $.getJSON wysyła
        // Accept: application/json bez sufiksu .json -> wykrywamy po nagłówku Accept.
        $isJson = strpos($this->getRequest()->getHeaderLine('Accept'), 'application/json') !== false;
        $search = trim((string)$this->getRequest()->getQuery('q'));
        if ($isJson && $search !== '') {
            $this->_search($search);

            return;
        }

        if ($this->getRequest()->getQuery('root')) {
            $query = strtolower($this->getRequest()->getQuery('root'));
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
        if ($root && $isJson) {
            $options = array_intersect_key(
                $this->getRequest()->getQuery(),
                ['perms' => null, 'urls' => null]
            );
            $cacheName = 'permissions_aco_' . $root->id;
            $permissions = Cache::read($cacheName, 'permissions');
            if ($permissions === null) {
                $permissions = $this->Permissions->format($acos, $aros, $options);
                Cache::write($cacheName, $permissions, 'permissions');
            }
        } else {
            $permissions = [];
        }

        $this->set(compact('aros', 'permissions'));

        if ($this->getRequest()->is('ajax') && isset($query)) {
            $this->render('Croogo/Acl.acl_permissions_table');
        } elseif ($isJson) {
            // Detekcja po naglowku Accept (bez _ext=json) NIE ustawia subDir 'json',
            // wiec szablon json/index.php (echo json_encode) wskazujemy jawnie.
            $this->viewBuilder()->disableAutoLayout();
            $this->setResponse($this->getResponse()->withType('application/json'));
            $this->render('json/index');
        } else {
            $this->_setPermissionRoots();
        }
    }

    /**
     * JSON for the filter above the permission tabs: the whole tree, not just the loaded level
     *
     * @param string $search Search terms
     * @return void
     */
    protected function _search(string $search): void
    {
        $roleList = $this->Roles->find('list');
        $aros = $this->Aros->getRoles($roleList);
        $found = $this->Acos->searchLeaves($search, self::SEARCH_LIMIT);
        $verdicts = $this->Permissions->roleVerdicts($found['acos'], $aros);

        $roles = [];
        foreach ($roleList as $roleId => $title) {
            $roles[] = ['id' => $roleId, 'title' => $title, 'aroId' => $aros[$roleId] ?? null];
        }
        $results = [];
        foreach ($found['acos'] as $acoId => $path) {
            $results[] = [
                'id' => $acoId,
                'path' => $path,
                'roles' => $verdicts[$acoId] ?? [],
                'damaged' => in_array($acoId, $found['damaged'], true),
            ];
        }
        $total = $found['total'];

        $this->set(compact('roles', 'results', 'total'));
        $this->viewBuilder()->disableAutoLayout();
        $this->setResponse($this->getResponse()->withType('application/json'));
        $this->render('json/search');
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
     * @return \Cake\Http\Response|void
     */
    public function toggle($acoId, $aroId)
    {
        if (!$this->getRequest()->is('ajax')) {
            return $this->redirect(['action' => 'index']);
        }

        // see if acoId and aroId combination exists
        $aro = $this->Aros->get($aroId);
        $path = $this->Acos->find('path', for: $acoId);
        $path = join('/', collection($path)->extract('alias')->toArray());

        $permitted = !$this->Permissions->check(['model' => $aro->model, 'foreign_key' => $aro->foreign_key], $path);
        $success = $this->Permissions->allow(['model' => $aro->model, 'foreign_key' => $aro->foreign_key], $path, '*', $permitted ? 1 : -1);
        if ($success) {
            $aco = $this->Acos->get($acoId);
            $cacheName = 'permissions_aco_' . $aco->parent_id;
            Cache::delete($cacheName, 'permissions');
            Cache::delete('permissions_public', 'permissions');
        }

        $this->viewBuilder()->enableAutoLayout(false);

        $this->set(compact('acoId', 'aroId', 'success', 'permitted'));
    }
}
