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
class AclFilterComponent extends Object {

/**
 * @param object $controller controller
 * @param array  $settings   settings
 */
    public function initialize(&$controller, $settings = array()) {
        $this->controller =& $controller;
    }

/**
 * Configure Auth component
 * Auth settings can be configured using Acl.Auth keys.
 * Currently, the following settings are applicable:
 *   - loginAction
 *   - loginRedirect
 *   - logoutRedirect
 *   - userScope
 *   - authError
 *   - loginError
 *   - fields
 */
    private function _setupAuth() {
        $this->controller->Auth->authorize = 'controller';

        $userModel = Configure::read('Acl.Auth.userModel');
        if (empty($userModel)) {
            $userModel = 'User';
            Configure::write('Acl.Auth.userModel', $userModel);
        }
        $this->controller->Auth->userModel = $userModel;

        $fields = Configure::read('Acl.Auth.fields');
        if (empty($fields)) {
            $fields = array('username' => 'username', 'password' => 'password');
            Configure::write('Acl.Auth.fields', $fields);
        }
        $this->controller->Auth->fields = $fields;

        $loginAction = array(
            'plugin' => null,
            'controller' => 'users',
            'action' => 'login',
        );
        if (!isset($this->controller->params['admin'])) {
            $loginAction = Set::merge($loginAction, Configure::read('Acl.Auth.loginAction'));
            Configure::write('Acl.Auth.loginAction', $loginAction);
        }
        $this->controller->Auth->loginAction = $loginAction;

        $logoutRedirect = Configure::read('Acl.Auth.logoutRedirect');
        if (empty($logoutRedirect)) {
            $logoutRedirect = array(
                'plugin' => null,
                'controller' => 'users',
                'action' => 'login',
                );
            Configure::write('Acl.Auth.logoutRedirect', $logoutRedirect);
        }
        $this->controller->Auth->logoutRedirect = $logoutRedirect;

        $loginRedirect = Configure::read('Acl.Auth.loginRedirect');
        if (empty($loginRedirect)) {
            $loginRedirect = array(
                'plugin' => null,
                'controller' => 'users',
                'action' => 'index',
                );
            Configure::write('Acl.Auth.loginRedirect', $loginRedirect);
        }
        $this->controller->Auth->loginRedirect = $loginRedirect;

        $userScope = Configure::read('Acl.Auth.userScope');
        if (empty($userScope)) {
            $userScope = array('User.status' => 1);
            Configure::write('Acl.Auth.userScope', $userScope);
        }
        $this->controller->Auth->userScope = $userScope;

        if ($authError = Configure::read('Acl.Auth.authError')) {
            $this->controller->authError = $authError;
        }

        if ($loginError = Configure::read('Acl.Auth.loginError')) {
            $this->controller->loginError = $loginError;
        }
    }

/**
 * acl and auth
 *
 * @return void
 */
    public function auth() {
        $this->_setupAuth();

        $this->controller->Auth->actionPath = 'controllers/';

        if ($this->controller->Auth->user() && $this->controller->Auth->user('role_id') == 1) {
            // Role: Admin
            $this->controller->Auth->allowedActions = array('*');
        } else {
            if ($this->controller->Auth->user()) {
                $roleId = $this->controller->Auth->user('role_id');
            } else {
                $roleId = 3; // Role: Public
            }

            $aro = $this->controller->Acl->Aro->find('first', array(
                'conditions' => array(
                    'Aro.model' => 'Role',
                    'Aro.foreign_key' => $roleId,
                ),
                'recursive' => -1,
            ));
            $aroId = $aro['Aro']['id'];
            $thisControllerNode = $this->controller->Acl->Aco->node($this->controller->Auth->actionPath.$this->controller->name);
            if ($thisControllerNode) {
                $thisControllerNode = $thisControllerNode['0'];
                $thisControllerActions = $this->controller->Acl->Aco->find('list', array(
                    'conditions' => array(
                        'Aco.parent_id' => $thisControllerNode['Aco']['id'],
                    ),
                    'fields' => array(
                        'Aco.id',
                        'Aco.alias',
                    ),
                    'recursive' => '-1',
                ));
                $thisControllerActionsIds = array_keys($thisControllerActions);
                $allowedActions = $this->controller->Acl->Aco->Permission->find('list', array(
                    'conditions' => array(
                        'Permission.aro_id' => $aroId,
                        'Permission.aco_id' => $thisControllerActionsIds,
                        'Permission._create' => 1,
                        'Permission._read' => 1,
                        'Permission._update' => 1,
                        'Permission._delete' => 1,
                    ),
                    'fields' => array(
                        'id',
                        'aco_id',
                    ),
                    'recursive' => '-1',
                ));
                $allowedActionsIds = array_values($allowedActions);
            }

            $allow = array();
            if (isset($allowedActionsIds) &&
                is_array($allowedActionsIds) &&
                count($allowedActionsIds) > 0) {
                foreach ($allowedActionsIds AS $i => $aId) {
                    $allow[] = $thisControllerActions[$aId];
                }
            }
            $this->controller->Auth->allowedActions = $allow;
        }
    }

}
?>