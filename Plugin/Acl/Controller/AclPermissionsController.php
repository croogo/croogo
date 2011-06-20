<?php
/**
 * AclPermissions Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclPermissionsController extends AclAppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    public $name = 'AclPermissions';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    public $uses = array(
        'Acl.AclAco',
        'Acl.AclAro',
        'Acl.AclArosAco',
        'Role',
    );

    function __acoList($tree, $parent = false, $level = 0) {
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
                $this->__acoList($leaf['children'], $path, ++$level);
                $level--;
                $children = count($leaf['children']);
                $acos[$leaf['Aco']['id']]['children'] = $children;
            } else {
                $acos[$leaf['Aco']['id']]['children'] = 0;
            }
        }
        return $acos;
    }

    function __generateAcoTreelist() {

        $acoConditions = array(
            'parent_id !=' => null,
            //'model' => null,
            'foreign_key' => null,
            'alias !=' => null,
        );
        $acos = $this->Acl->Aco->find('threaded', array('conditions' => $acoConditions));
        $acos = $this->__acoList($acos);

        $paths = Set::extract('/path', $acos);
        foreach ($acos as $id => &$aco) {
            if (strpos($aco['path'], '/') === false) {
                $childcount = count(preg_grep('/'. $aco['path'] . '\//', $paths));
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

        foreach ($acos  as $id => &$aco) {
            $path = $aco['path'];
            $childcount = $aco['children'];
            if ($childcount == 0) {
                $type = 'action';
            } else {
                if ($aco['grandchildren'] > 0) {
                    $type = 'plugin';
                } else {
                    $type = 'controller';
                }
            }

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

    public function admin_index() {
        $this->set('title_for_layout', __('Permissions', true));

        $acos = $this->__generateAcoTreelist();
        $roles = $this->Role->find('list');

        $this->set(compact('acos', 'roles'));

        $rolesAros = $this->AclAro->find('all', array(
            'conditions' => array(
                'AclAro.model' => 'Role',
                'AclAro.foreign_key' => array_keys($roles),
            ),
        ));
        $rolesAros = Set::combine($rolesAros, '{n}.AclAro.foreign_key', '{n}.AclAro.id');

        $permissions = array(); // acoId => roleId => bool
        foreach ($acos AS $acoId => $aco) {
            if (substr_count($aco[0], '-') != 0) {
                $permission = array();
                foreach ($roles AS $roleId => $roleTitle) {
                    $hasAny = array(
                        'aco_id'  => $acoId,
                        'aro_id'  => $rolesAros[$roleId],
                        '_create' => 1,
                        '_read'   => 1,
                        '_update' => 1,
                        '_delete' => 1,
                    );
                    if ($this->AclArosAco->hasAny($hasAny)) {
                        $permission[$roleId] = 1;
                    } else {
                        $permission[$roleId] = 0;
                    }
                    $permissions[$acoId] = $permission;
                }
            }
        }
        $this->set(compact('rolesAros', 'permissions'));
    }

    public function admin_toggle($acoId, $aroId) {
        if (!$this->RequestHandler->isAjax()) {
            $this->redirect(array('action' => 'index'));
        }

        // see if acoId and aroId combination exists
        $conditions = array(
            'AclArosAco.aco_id' => $acoId,
            'AclArosAco.aro_id' => $aroId,
        );
        if ($this->AclArosAco->hasAny($conditions)) {
            $data = $this->AclArosAco->find('first', array('conditions' => $conditions));
            if ($data['AclArosAco']['_create'] == 1 &&
                $data['AclArosAco']['_read'] == 1 &&
                $data['AclArosAco']['_update'] == 1 &&
                $data['AclArosAco']['_delete'] == 1) {
                // from 1 to 0
                $data['AclArosAco']['_create'] = 0;
                $data['AclArosAco']['_read'] = 0;
                $data['AclArosAco']['_update'] = 0;
                $data['AclArosAco']['_delete'] = 0;
                $permitted = 0;
            } else {
                // from 0 to 1
                $data['AclArosAco']['_create'] = 1;
                $data['AclArosAco']['_read'] = 1;
                $data['AclArosAco']['_update'] = 1;
                $data['AclArosAco']['_delete'] = 1;
                $permitted = 1;
            }
        } else {
            // create - CRUD with 1
            $data['AclArosAco']['aco_id'] = $acoId;
            $data['AclArosAco']['aro_id'] = $aroId;
            $data['AclArosAco']['_create'] = 1;
            $data['AclArosAco']['_read'] = 1;
            $data['AclArosAco']['_update'] = 1;
            $data['AclArosAco']['_delete'] = 1;
            $permitted = 1;
        }

        // save
        $success = 0;
        if ($this->AclArosAco->save($data)) {
            $success = 1;
        }

        $this->set(compact('acoId', 'aroId', 'data', 'success', 'permitted'));
    }
    
    function admin_upgrade() {
        App::import('Component', 'Acl.AclUpgrade');
        $this->AclUpgrade = new AclUpgradeComponent;
        $this->AclUpgrade->initialize($this);
        if (($errors = $this->AclUpgrade->upgrade()) === true) {
            $this->Session->setFlash(__('Acl Upgrade complete', true));
        } else {
            $message = '';
            foreach ($errors as $error) {
                $message .= $error . '<br />';
            }
			$this->Session->setFlash($message);
        }
        $this->redirect($this->referer());
    }

}
?>