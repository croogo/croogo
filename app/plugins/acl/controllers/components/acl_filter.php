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
    function initialize(&$controller, $settings = array()) {
		$this->controller =& $controller;
	}

    /**
     * acl and auth
     *
     * @return void
     */
    function auth() {
        //Configure AuthComponent
	    $this->controller->Auth->authorize = 'actions';
	    $this->controller->Auth->loginAction = array('plugin' => 0, 'controller' => 'users', 'action' => 'login');
	    $this->controller->Auth->logoutRedirect = array('plugin' => 0, 'controller' => 'users', 'action' => 'login');
	    $this->controller->Auth->loginRedirect = array('plugin' => 0, 'controller' => 'users', 'action' => 'index');
        $this->controller->Auth->userScope = array('User.status' => 1);
		$this->controller->Auth->actionPath = 'controllers/';

        if ($this->controller->Auth->user() &&
            $this->controller->Auth->user('role_id') == 1) {
            // Role: Admin
            $this->controller->Auth->allowedActions = array('*');
        } else {
            if ($this->controller->Auth->user()) {
                $roleId = $this->controller->Auth->user('role_id');
            } else {
                $roleId = 3; // Role: Public
            }

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
                ));
                $thisControllerActionsIds = array_keys($thisControllerActions);
                $conditions = array(
                    'AclArosAco.aro_id' => $roleId,
                    'AclArosAco.aco_id' => $thisControllerActionsIds,
                    'AclArosAco._create' => 1,
                    'AclArosAco._read' => 1,
                    'AclArosAco._update' => 1,
                    'AclArosAco._delete' => 1,
                );
                $allowedActions = ClassRegistry::init('Acl.AclArosAco')->find('all', array(
                    'recursive' => '-1',
                    'conditions' => $conditions,
                ));
                $allowedActionsIds = Set::extract('/AclArosAco/aco_id', $allowedActions);
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