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
            $cacheName = 'permissions_' . strval($user['id']);
            if (($permissions = Cache::read($cacheName, 'permissions')) === false) {
                $permissions = $this->getPermissions();
                Cache::write($cacheName, $permissions, 'permissions');
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

/** __formatAcoTree
 *
 *  Internally used by AclFilter::acoTreelist.
 *  An extra field is created for the number of children for each ACO.
 *
 *  @param tree tree/subtree of ACOs retrieved via find('threaded')
 *  @return array TreeBehavior::generatetreelist compatible array
 *  @see AclFilterComponent::acoTreelist
 */
    private function __formatAcoTree($tree, $parent = false, $level = 0) {
        static $acos = array();
        foreach ($tree as $i => $leaf) {
            $alias = str_pad($leaf['Aco']['alias'], strlen($leaf['Aco']['alias']) + $level, '-', STR_PAD_LEFT);
            if ($parent) {
                $path = $parent . '/' . $leaf['Aco']['alias'];
            } else {
                $path = $leaf['Aco']['alias'];
            }
            $acos[$leaf['Aco']['id']] = array(
                $alias,
                'path' => $path,
                );
            if (!empty($leaf['children'])) {
                $this->__formatAcoTree($leaf['children'], $path, ++$level);
                $level--;
                $children = count($leaf['children']);
                $acos[$leaf['Aco']['id']]['children'] = $children;
            } else {
                $acos[$leaf['Aco']['id']]['children'] = 0;
            }
        }
        return $acos;
    }

/** acoTreeList
 *
 *  Recursive function to construct a generatetreelist() compatible array.
 */
    public function acoTreelist() {
        $acoConditions = array(
            'parent_id !=' => null,
            //'model' => null,
            'foreign_key' => null,
            'alias !=' => null,
        );
        $acos = $this->controller->Acl->Aco->find('threaded', array('conditions' => $acoConditions));
        $acos = $this->__formatAcoTree($acos);

        // calculate the number of grandchildren for each ACO
        $paths = Set::extract('/path', $acos);
        foreach ($acos as $id => &$aco) {
            if (strpos($aco['path'], '/') === false) {
                $childcount = count(preg_grep('/^'. $aco['path'] . '\//', $paths));
            } else {
                $aco['grandchildren'] = 0;
                continue;
            }
            if ($aco['children'] == 0 || $aco['children'] == $childcount) {
                $aco['grandchildren'] = 0;
            } else {
                $aco['grandchildren'] = $childcount;
            }
        }

        // now that we have count of children and grandchildren, populate the rest of fields
        foreach ($acos  as $id => &$aco) {
            $path = $aco['path'];
            $childcount = $aco['children'];

            // determine type of ACO
            if ($childcount == 0) {
                $type = 'action';
            } else {
                if ($aco['grandchildren'] > 0) {
                    $type = 'plugin';
                    if ($aco['grandchildren'] == $aco['children']) {
                        $type = 'controller';
                    }
                } else {
                    $type = 'controller';
                }
            }

            // determine value of plugin, controller, and action names
            if ($type == 'plugin') {
                $plugin = $path; $controller = false; $action = false;
            } else {
                $c = substr_count($path, '/');
                $pathE = explode('/', $path);
                if ($type == 'action') {
                    if ($c == 2) {
                        $plugin = $pathE[0];
                        $controller = $pathE[1];
                        $action = $pathE[2];
                    } elseif ($c == 1) {
                        $plugin = false;
                        $controller = $pathE[0];
                        $action = $pathE[1];
                    }
                } elseif ($type == 'controller') {
                    if ($c == 1) {
                        $plugin = $pathE[0];
                        $controller = $pathE[1];
                        $action = '';
                    } elseif ($c == 0) {
                        $plugin = false;
                        $controller = $pathE[0];
                        $action = '';
                    }
                }
            }

            $aco = Set::merge($aco, array(
                'type' => $type,
                'children' => $childcount,
                'plugin' => $plugin,
                'controller' => $controller,
                'action' => $action,
                ));
        }
        return $acos;
    }

}
?>