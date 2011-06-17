<?php
/**
 * AclFilter Component
 *
 * PHP version 5
 *
 * @category Component
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclFilterComponent extends Component {

    protected $controller = null;

/**
 * Authorized
 * Contains a list of actions authorized for logged in user. This list is
 * automatically populated and used by AppController::isAuthorized
 *
 * @var array
 * @access public
 * @see AclFilterComponent::getPermissions()
 * @see AppController::isAuthorized()
 */
    var $authorized = array();

/**
 * @param object $controller controller
 * @param array  $settings   settings
 */
    public function initialize(&$controller) {
        $this->controller =& $controller;
    }

/**
 * acl and auth
 *
 * @return void
 */
    public function auth() {
        //Configure AuthComponent
        $this->controller->Auth->authenticate = array(
            AuthComponent::ALL => array(
                'userModel' => 'User',
                'fields' => array(
                    'username' => 'username',
                    ),
                'scope' => array(
                    'User.status' => 1,
                    ),
                ),
            'Form',
            );
        $actionPath = 'controllers';
        $this->controller->Auth->authorize = array(
            AuthComponent::ALL => array('actionPath' => $actionPath),
            'Controller',
            );
        $this->controller->Auth->loginAction = array(
            'plugin' => null,
            'controller' => 'users',
            'action' => 'login',
        );
        $this->controller->Auth->logoutRedirect = array(
            'plugin' => null,
            'controller' => 'users',
            'action' => 'login',
        );
        $this->controller->Auth->loginRedirect = array(
            'plugin' => null,
            'controller' => 'users',
            'action' => 'index',
        );

        $user = $this->controller->Auth->user();
        if (!empty($user['role_id']) && $user['role_id'] == 1) {
            // Role: Admin
            $this->controller->Auth->allowedActions = array('*');
        } else {
            $permKey = 'Permission';
            if ($this->controller->Session->check($permKey)) {
                $permissions = $this->controller->Session->read($permKey);
            } else {
                $permissions = $this->getPermissions();
                $this->controller->Session->write($permKey, $permissions);
            }

            if (!empty($user['id'])) {
                // let Controller::isAuthorized work
                $this->authorized = $permissions['authorized'];
                return;
            }

            if (!empty($permissions['allowed'][$this->controller->name])) {
                $this->controller->Auth->allow($permissions['allowed'][$this->controller->name]);
            }
        }

    }

/**
 * getPermissions
 * retrieve list of permissions from database
 * @return array list of authorized and allowed actions
 */
    function getPermissions() {
        $Acl =& $this->controller->Acl;
        if (! $this->controller->Auth->user()) {
            $aro = array('model' => 'Role', 'foreign_key' => 3);
        } else {
            $aro = array('model' => 'User', 'foreign_key' => $this->controller->Auth->user('id'));
        }
        $node = $Acl->Aro->node($aro);
        $nodes = $Acl->Aro->getPath($aro);

        $aros = Set::extract('/Aro/id', $node);
        if (!empty($nodes)) {
            $aros = Set::merge($aros, Set::extract('/Aro/id', $nodes));
        }

        $permissions = $Acl->Aro->Permission->find('all', array(
            'conditions' => array(
                'Permission.aro_id' => $aros,
                'Permission._create' => 1,
                'Permission._read' => 1,
                'Permission._update' => 1,
                'Permission._delete' => 1,
                )
            ));

        $authorized = $allowedActions = array();
        foreach ($permissions as $permission) {
            $path = $Acl->Aco->getPath($permission['Permission']['aco_id']);
            if (count($path) == 4) {
                // plugin controller/action
                $controller = $path[2]['Aco']['alias'];
                $action = $path[3]['Aco']['alias'];
            } else {
                // core controller/action
                $controller = $path[1]['Aco']['alias'];
                $action = $path[2]['Aco']['alias'];
            }
            $allowedActions[$controller][] = $action;
            $authorized[] = implode('/', Set::extract('/Aco/alias', $path));
        }
        return array('authorized' => $authorized, 'allowed' => $allowedActions);
    }

}
?>