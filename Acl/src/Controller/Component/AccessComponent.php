<?php

namespace Croogo\Acl\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Croogo\Core\Croogo;

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
        $controller = $event->getSubject();
        $this->_controller = $controller;
        if ($controller->request->getParam('prefix') != 'admin') {
            return;
        }

        switch ($controller->getName()) {
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
        $element = 'Croogo/Acl.admin/parent_role';
        Croogo::hookAdminTab('Admin/Roles/add', $title, $element);
        Croogo::hookAdminTab('Admin/Roles/edit', $title, $element);

        $id = null;
        if (!empty($this->_controller->request->getParam('pass')[0])) {
            $id = $this->_controller->request->getParam('pass')[0];
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

    public function isUrlAuthorized($user, $url)
    {
        if (is_string($url)) {
            $request = new ServerRequest($url);
            $params = Router::parseRequest($request);
            $request = $request->withAttribute('params', $params);
        } else {
            $request = new ServerRequest();
            $params = Router::reverse($url);
            $request = $request->withAttribute('params', $params);
        }

        return $this->getController()->Auth->isAuthorized($user, $request);
    }
}
