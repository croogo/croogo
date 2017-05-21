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
