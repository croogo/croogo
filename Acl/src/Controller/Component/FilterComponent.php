<?php

namespace Croogo\Acl\Controller\Component;

use Cake\Cache\Cache;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Controller\Component;
use Cake\Controller\Component\AuthComponent;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Croogo\Core\Croogo;
use Croogo\Core\Utility\StringConverter;

/**
 * AclFilter Component
 *
 * @category Component
 * @package  Croogo.Acl.Controller.Component
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class FilterComponent extends Component
{

/**
 * _controller
 *
 * @var Controller
 */
    protected $_controller = null;

/**
 * beforeFilter
 *
 * @param Event $event instance of event
 * @return void
 */
    public function beforeFilter(Event $event)
    {
        $this->_controller = $event->subject();

        if ($this->_config('multiRole')) {
            Croogo::hookAdminTab('Admin/Users/add', 'Roles', 'Croogo/Acl.admin/roles');
            Croogo::hookAdminTab('Admin/Users/edit', 'Roles', 'Croogo/Acl.admin/roles');

        }
    }

/**
 * Helper function to retrieve value from `Access Control` settings
 *
 * @return mixed null when config key is not found
 */
    protected function _config($key)
    {
        static $config = null;
        if (empty($config)) {
            $config = Configure::read('Access Control');
        }
        if (array_key_exists($key, (array)$config)) {
            return $config[$key];
        }
        return null;
    }

/**
 * configure component settings
 *
 * @return void
 */
    protected function _configure()
    {
        if (!$this->_registry->has('Croogo/Acl.AutoLogin')) {
            $this->_registry->load('Croogo/Acl.AutoLogin');
            if (!$this->_registry->has('Cookie')) {
                $this->_registry->load('Cookie');
            }
        }

        //Configure AuthComponent
        $this->_controller->Auth->config('authenticate', [
            AuthComponent::ALL => [
                'userModel' => 'Croogo/Users.Users',
                'fields' => [
                    'username' => 'username',
                    'password' => 'password',
                ],
                'passwordHasher' => [
                    'className' => 'Fallback',
                    'hashers' => ['Default', 'Weak']
                ],
                'scope' => [
                    'Users.status' => 1,
                ],
            ],
        ]);
        if ($this->_config('autoLoginDuration')) {
            if (!function_exists('mcrypt_encrypt')) {
                $notice = __d('croogo', '"AutoLogin" (Remember Me) disabled since mcrypt_encrypt is not available');
                $this->log($notice, LOG_CRIT);
                if (isset($this->_controller->request->params['admin'])) {
                    $this->_controller->Flash->error($notice);
                }
                if (isset($this->_controller->Settings)) {
                    $Setting = $this->_controller->Settings;
                } else {
                    $Setting = TableRegistry::get('Croogo/Settings.Settings');
                }
                $Setting->write('Access Control.autoLoginDuration', '');
            }
            $this->_controller->Auth->config('authenticate', ['Croogo/Acl.Cookie']);
        }
        if ($this->_config('multiColumn')) {
            $this->_controller->Auth->config('authenticate', ['Croogo/Acl.MultiColumn']);
        } else {
            $this->_controller->Auth->config('authenticate', ['Form']);
        }

        $this->_controller->Auth->config('authorize', [
            AuthComponent::ALL => [
                'actionPath' => 'controllers',
                'userModel' => 'Croogo/Users.Users',
            ],
            'Croogo/Acl.AclCached' => [
                'actionPath' => 'controllers',
            ]
        ]);

        if ($this->_controller->request->param('prefix') == 'admin' &&
            !$this->_controller->Auth->user()) {
            $this->_controller->Auth->config('authError', false);
        }

        $this->configureLoginActions();
    }

/**
 * Load login actions configurations
 *
 * @return void
 */
    public function configureLoginActions()
    {
        $this->_controller->Auth->config('loginAction', [
            'prefix' => false,
            'plugin' => 'Croogo/Users',
            'controller' => 'Users',
            'action' => 'login',
        ]);
        if ($this->request->param('prefix') === 'admin') {
            $this->_controller->Auth->config('loginAction', [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Users',
                'controller' => 'Users',
                'action' => 'login',
            ]);
        }
        $this->_controller->Auth->config('logoutRedirect', [
            'plugin' => 'Croogo/Users',
            'controller' => 'Users',
            'action' => 'login',
        ]);
        if ($this->_controller->request->param('prefix') == 'admin') {
            $dashboardUrl = Configure::read('Site.dashboard_url');
            if (is_string($dashboardUrl)) {
                $converter = new StringConverter();
                $dashboardUrl = $converter->linkStringToArray($dashboardUrl);
            }
            $loginRedirect = $dashboardUrl ?: '/admin';
            $this->_controller->Auth->config('loginRedirect', $loginRedirect);
        } else {
            $loginRedirect = Configure::read('Croogo.homeUrl') ?: '/';
            $this->_controller->Auth->config('loginRedirect', $loginRedirect);
        }

        if ($this->_controller->request->is('ajax')) {
            $this->_controller->Auth->config('unauthorizedRedirect', false);
        } else {
            $this->_controller->Auth->config('unauthorizedRedirect', [
                'plugin' => 'Croogo/Users',
                'controller' => 'Users',
                'action' => 'login',
            ]);
        }

        $config = Configure::read('Acl');
        if (!empty($config['Auth']) && is_array($config['Auth'])) {
            $isAdminRequest = !empty($this->_controller->request->params['admin']);
            $authActions = [
                'loginAction', 'loginRedirect', 'logoutRedirect',
                'unauthorizedRedirect',
            ];
            foreach ($config['Auth'] as $property => $value) {
                $isAdminRoute = !empty($value['admin']);
                $isAuthAction = in_array($property, $authActions);
                if (!is_string($value) && $isAdminRequest !== $isAdminRoute && $isAuthAction) {
                    continue;
                }
                $this->_controller->Auth->config($property, $value);
            }
        }
    }

/**
 * acl and auth
 *
 * @return void
 */
    public function auth()
    {
        $this->_configure();
        $user = $this->_controller->Auth->user();

        // authorization for authenticated user is handled by authorize object
        if ($user) {
            return;
        }

        $authorizer = $this->_controller->Auth->getAuthorize('Croogo/Acl.AclCached');

        if ($this->_controller->Acl->check('Role-public', $authorizer->action($this->_controller->request))) {
            $this->_controller->Auth->allow(
                $this->_controller->request->action
            );
        }
    }
}
