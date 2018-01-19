<?php

namespace Croogo\Acl\Auth;

use Acl\Auth\BaseAuthorize;
use Cake\Cache\Cache;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Log\Log;
use Cake\Network\Request;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Inflector;

use Psr\Log\LogLevel;

/**
 * An authentication adapter for AuthComponent. Provides similar functionality
 * to ActionsAuthorize class from CakePHP core _with_ caching capability.
 *
 * @package  Croogo.Acl.Controller.Component.Auth
 * @since    1.5
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @see      RowLevelAclComponent
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclCachedAuthorize extends BaseAuthorize
{

    protected $_defaultConfig = [
        'actionMap' => [
            'toggle' => 'update',
            'moveup' => 'update',
            'movedown' => 'update',
            'process' => 'delete',
            'index' => 'read',
            'add' => 'create',
            'edit' => 'update',
            'view' => 'read',
            'remove' => 'delete',
            'create' => 'create',
            'read' => 'read',
            'update' => 'update',
            'delete' => 'delete',
        ],
    ];

/**
 * Constructor
 */
    public function __construct(ComponentRegistry $registry, $config = [])
    {
        parent::__construct($registry, $config);
    }

/**
 * Checks whether $user is an administrator
 *
 * @param bool True if user has administrative role
 */
    protected function _isAdmin($user)
    {
        static $Role = null;
        if (empty($user['role_id'])) {
            return false;
        }
        if (empty($this->_adminRole)) {
            if (empty($Role)) {
                $Role = TableRegistry::get('Croogo/Users.Roles');
                $Role->addBehavior('Croogo/Core.Aliasable');
            }
            $this->_adminRole = $Role->byAlias('superadmin');
        }
        return $user['role_id'] == $this->_adminRole;
    }

/**
 * Get the action path for a given request.
 *
 * @see BaseAuthorize::action()
 */
    public function action(Request $request, $path = '/:plugin/:prefix/:controller/:action')
    {
        $apiPath = Configure::read('Croogo.Api.path');
        if (!$request->is('api')) {
            return parent::action($request, $path);
        }

        $api = isset($request['api']) ? $apiPath : null;
        if (isset($request['prefix'])) {
            $prefix = $request['prefix'];
            $action = str_replace($request['prefix'] . '_', '', $request['action']);
        } else {
            $prefix = null;
            $action = $request['action'];
        }
        $plugin = empty($request['plugin']) ? null : str_replace('/', '\\', Inflector::camelize($request['plugin']));
        $controller = Inflector::camelize($request['controller']);

        $path = str_replace(
            [$apiPath, ':prefix', ':plugin', ':controller', ':action'],
            [$api, $prefix, $plugin, $controller, $action],
            $this->config('actionPath') . $path
        );
        $path = str_replace('//', '/', $path);
        return trim($path, '/');
    }

/**
 * check request request authorization
 *
 */
    public function authorize($user, Request $request)
    {
        // Admin role is allowed to perform all actions, bypassing ACL
        if ($this->_isAdmin($user)) {
            return true;
        }

        $allowed = false;
        $Acl = $this->_registry->load('Acl');
        list($plugin, $userModel) = pluginSplit($this->config('userModel'));

        $action = $this->action($request);

        $cacheName = 'permissions_' . strval($user['id']);
        if (($permissions = Cache::read($cacheName, 'permissions')) === false) {
            $permissions = [];
            Cache::write($cacheName, $permissions, 'permissions');
        }

        if (!isset($permissions[$action])) {
            $userTable = TableRegistry::get($this->config('userModel'));
            $user = $userTable->get($user['id']);
            $allowed = $Acl->check($user, $action);
            $permissions[$action] = $allowed;
            Cache::write($cacheName, $permissions, 'permissions');
            $hit = false;
        } else {
            $allowed = $permissions[$action];
            $hit = true;
        }

        if (Configure::read('debug')) {
            $status = $allowed ? ' allowed.' : ' denied.';
            $cached = $hit ? ' (cache hit)' : ' (cache miss)';
            Log::write(LOG_ERR, $user['username'] . ' - ' . $action . $status . $cached);
        }

        if (!$allowed) {
            return false;
        }

        if (!Configure::read('Access Control.rowLevel')) {
            return $allowed;
        }

        // bail out when controller's primary model does not want row level acl
        $controller = $this->_registry->getController();
        $model = $controller->name;
        $Model = $controller->{$model};
        if ($Model && !$Model->behaviors()->has('RowLevelAcl')) {
            return $allowed;
        }

        $primaryKey = $Model->primaryKey();
        $ids = [];
        if ($request->is('get') && $request->param('pass.0')) {
            // collect id from actions such as: Nodes/admin_edit/1
            $ids[] = $request->param('pass.0');
        } elseif ($request->is('post') || $request->is('put')) {

            $action = $request->data('action');
            if ($action) {
                // collect ids from 'bulk' processing action
                foreach ($request->data[$model] as $id => $flag) {
                    if (isset($flag[$primaryKey]) && $flag[$primaryKey] == 1) {
                        $ids[] = $id;
                    }
                }
            }

            $id = $request->param('pass.0');
            if ($id) {
                $ids[] = $id;
            }
        }

        foreach ($ids as $id) {
            if (is_numeric($id)) {
                try {
                    $allowed = $this->_authorizeByContent($user, $request, $id);
                } catch (\Exception $e) {
                    $allowed = false;
                }
            } else {
                continue;
            }
            if (!$allowed) {
                break;
            }
        }

        return $allowed;
    }

/**
 * Checks authorization by content
 *
 * @throws Exception
 */
    protected function _authorizeByContent($user, Request $request, $id)
    {
        if (!isset($this->config('actionMap')[$request->params['action']])) {
            $message = __d('croogo',
                '_authorizeByContent() - Access of un-mapped action "%1$s" in controller "%2$s"',
                $request->action,
                $request->controller
            );
            Log::critical($message);
            throw new Exception($message);
        }

        list($plugin, $userModel) = pluginSplit($this->config('userModel'));
        $acoNode = [
            'model' => $this->_registry->getController()->name,
            'foreign_key' => $id,
        ];
        $alias = sprintf('%s.%s', $acoNode['model'], $acoNode['foreign_key']);
        $action = $this->config('actionMap')[$request->param('action')];

        $cacheName = 'permissions_content_' . strval($user['id']);
        if (($permissions = Cache::read($cacheName, 'permissions')) === false) {
            $permissions = [];
            Cache::write($cacheName, $permissions, 'permissions');
        }

        if (!isset($permissions[$alias][$action])) {
            $Acl = $this->_registry->load('Acl');
            try {
                $allowed = $Acl->check([$userModel => $user], $acoNode, $action);
            } catch (\Exception $e) {
                Log::warning('authorizeByContent: ' . $e->getMessage());
                $allowed = false;
            }
            $permissions[$alias][$action] = $allowed;
            Cache::write($cacheName, $permissions, 'permissions');
            $hit = false;
        } else {
            $allowed = $permissions[$alias][$action];
            $hit = true;
        }

        if (Configure::read('debug')) {
            $status = $allowed ? ' allowed.' : ' denied.';
            $cached = $hit ? ' (cache hit)' : ' (cache miss)';
            Log::write(LOG_ERR, $user['username'] . ' - ' . $action . '/' . $id . $status . $cached);
        }
        return $allowed;
    }
}
