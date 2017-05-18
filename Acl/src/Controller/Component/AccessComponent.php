<?php

namespace Croogo\Acl\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 * AclAccess Component provides various methods to manipulate Aros and Acos,
 * and additionaly setup various settings for backend/admin use.
 *
 * @category Component
 * @package  Croogo.Acl.Controller.Component
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AccessComponent extends Component
{

/**
 * _controller
 *
 * @var Controller
 */
    protected $_controller = null;

/**
 * startup
 *
 * @param Event $event
 */
    public function startup(Event $event)
    {
        $controller = $event->subject();
        $this->_controller = $controller;
        $adminPrefix = isset($controller->request->params['admin']);
        if (!$adminPrefix) {
            return;
        }

        switch ($controller->name) {
            case 'Roles':
                $this->_setupRole();
                break;
        }
    }

/**
 * Hook admin menu element to set role parent
 */
    protected function _setupRole()
    {
        $title = __d('croogo', 'Parent Role');
        $element = 'Acl.admin/parent_role';
        Croogo::hookAdminTab('Croogo/Users.Admin/Roles/add', $title, $element);
        Croogo::hookAdminTab('Croogo/Users.Admin/Roles/edit', $title, $element);

        $this->_controller->Role->bindAro();
        $id = null;
        if (!empty($this->_controller->request->params['pass'][0])) {
            $id = $this->_controller->request->params['pass'][0];
        }
        $this->_controller->set('parents', $this->_controller->Roles->allowedParents($id));
    }

/**
 * Add ACO
 *
 * Creates ACOs with permissions for roles.
 *
 * Action Path format:
 * - ControllerName
 * - ControllerName/method_name
 *
 * @param string $action action path
 * @param array $allowRoles Role aliases
 * @return void
 */
    public function addAco($action, $allowRoles = [])
    {
        $actionPath = $this->_controller->Auth->config('authorize.all.actionPath');
        if (strpos($action, $actionPath) === false) {
            $action = str_replace('//', '/', $actionPath . '/' . $action);
        }
        $Aco = TableRegistry::get('Croogo/Acl.Acos');
        $Aco->addAco($action, $allowRoles);
    }

/**
 * Remove ACO
 *
 * Removes ACOs and their Permissions
 *
 * Action Path format:
 * - ControllerName
 * - ControllerName/method_name
 *
 * @param string $action action path
 * @return void
 */
    public function removeAco($action)
    {
        $actionPath = $this->_controller->Auth->authorize['all']['actionPath'];
        if (strpos($action, $actionPath) === false) {
            $action = str_replace('//', '/', $actionPath . '/' . $action);
        }
        $Aco = TableRegistry::get('Croogo/Acl.Acos');
        $Aco->removeAco($action);
    }
}
