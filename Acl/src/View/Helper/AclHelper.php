<?php

namespace Croogo\Acl\View\Helper;

use Acl\Controller\Component\AclComponent;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\View\Helper;
use Cake\View\View;

/**
 * Acl Helper
 *
 * @category Helper
 * @package  Croogo.Acl
 * @version  1.4
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclHelper extends Helper
{

/**
 * Cached actions per Role
 *
 * @var array
 * @access public
 */
    public $allowedActions = [];

/**
 * Path Whitelist
 */
    protected $_pathWhitelist = ['/', '#'];

/**
 * Constructor
 */
    public function __construct(View $View, $settings = [])
    {
        $this->settings = Hash::merge([
            'pathWhitelist' => $this->_pathWhitelist
        ], $settings);
        parent::__construct($View, $settings);
        $plugin = 'Croogo/Acl';
        /* TODO: App::uses('AclPermission', $plugin . '.Model'); */
        $this->Permissions = TableRegistry::get($plugin . '.Permissions');

        $this->Acl = new AclComponent(new ComponentRegistry());
    }

/**
 * Checks whether path is in whitelist
 *
 * @param string $path Path
 * @return bool True if path is in the whitelist
 */
    protected function _isWhitelist($url)
    {
        return in_array($url, (array)$this->settings['pathWhitelist']);
    }

/**
 * beforeRender
 *
 */
    public function beforeRender($viewFile)
    {
        // display upgrade link when required
        $key = AuthComponent::$sessionKey . '.aclUpgrade';
        if ($this->_View->Session->read($key)) {
            $link = $this->_View->Croogo->adminAction(
                __d('croogo', 'Upgrade Acl database'),
                ['controller' => 'acl_permissions', 'action' => 'upgrade'],
                ['button' => 'primary']
            );
            $this->_View->Blocks->append('actions', $link);
        }
    }

/**
 * Returns an array of allowed actions for current logged in Role
 *
 * @param int$roleIdRole id
 * @return array
 */
    public function getAllowedActionsByRoleId($roleId)
    {
        if (!empty($this->allowedActions[$roleId])) {
            return $this->allowedActions[$roleId];
        }

        $this->allowedActions[$roleId] = $this->Permissions->getAllowedActionsByRoleId($roleId);
        return $this->allowedActions[$roleId];
    }

/**
 * Check if url is allowed for the Role
 *
 * @param int$roleIdRole id
 * @param $url array
 * @return boolean
 */
    public function linkIsAllowedByRoleId($roleId, $url)
    {
        if (is_string($url)) {
            return $this->_isWhitelist($url);
        }
        if (isset($url['admin']) && $url['admin'] == true) {
            $url['action'] = 'admin_' . $url['action'];
        }
        $plugin = empty($url['plugin']) ? null : Inflector::camelize($url['plugin']) . '/';
        $path = '/:plugin/:controller/:action';
        $path = str_replace(
            [':controller', ':action', ':plugin/'],
            [Inflector::camelize($url['controller']), $url['action'], $plugin],
            'controllers/' . $path
        );
        $linkAction = str_replace('//', '/', $path);
        if (in_array($linkAction, $this->getAllowedActionsByRoleId($roleId))) {
            return true;
        }
        return false;
    }

/**
 * Returns an array of allowed actions for current logged in User
 *
 * @param int $userId User Id
 * @return array
 */
    public function getAllowedActionsByUserId($userId)
    {
        if (!empty($this->allowedActions[$userId])) {
            return $this->allowedActions[$userId];
        }

        $this->allowedActions[$userId] = $this->Permissions->getAllowedActionsByUserId($userId);
        return $this->allowedActions[$userId];
    }

/**
 * Check if url is allowed for the User
 *
 * @param int $userId User Id
 * @param array|string $url link/url to check
 * @return boolean
 */
    public function linkIsAllowedByUserId($userId, $url)
    {
        if (is_array($url)) {
            if (isset($url['admin']) && $url['admin'] == true && empty($url['prefix'])) {
                $url['prefix'] = 'admin';
            }
            $prefix = isset($url['prefix']) ? $url['prefix'] : null;
            $plugin = empty($url['plugin']) ? null : str_replace('/', '\\', Inflector::camelize($url['plugin'])) . '/';
            $controller = empty($url['controller']) ? null : $url['controller'];
            $action = empty($url['action']) ? null : $url['action'];
            $path = '/:plugin/:prefix/:controller/:action';
            $path = str_replace(
                [ ':plugin/', ':prefix', ':controller', ':action' ],
                [
                    $plugin,
                    Inflector::camelize($prefix),
                    Inflector::camelize($controller),
                    $action,
                ],
                'controllers/' . $path
            );
        } else {
            if ($this->_isWhitelist($url)) {
                return true;
            }
            $path = $url;
        }
        $linkAction = str_replace('//', '/', $path);

        // FIXME: need to convert from plain string url to acl format
        if ($linkAction == '/') {
            $linkAction = 'controllers/Croogo\\Nodes/Nodes/promoted';
        }
        if ($linkAction == '/admin') {
            $linkAction = 'controllers/Croogo\\Dashboards/Admin/Dashboards/dashboard';
        }

        $userAro = ['model' => 'Users', 'foreign_key' => $userId];
        if ($this->Acl->check($userAro, $linkAction, '*')) {
            return true;
        }
        return false;
    }

}
