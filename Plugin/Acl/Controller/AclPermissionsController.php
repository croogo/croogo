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

    public function admin_index() {
        $this->set('title_for_layout', __('Permissions'));

        $acoConditions = array(
            'parent_id !=' => null,
            //'model' => null,
            'foreign_key' => null,
            'alias !=' => null,
        );
        $acos  = $this->Acl->Aco->generateTreeList($acoConditions, '{n}.Aco.id', '{n}.Aco.alias', '-');
        $roles = $this->Role->find('list');
        $this->set(compact('acos', 'roles'));

        $acoPaths = array();
        foreach ($acos  as $id => $alias) {
            $paths = $this->Acl->Aco->getPath($id);
            unset($paths[0]);
            $alias = implode('/', Set::extract('/Aco/alias', $paths));
            $childcount = $this->Acl->Aco->childCount($id, true);
            if ($childcount == 0) {
                $type = 'action';
            } else {
                $grandchildcount = $this->Acl->Aco->childCount($id);
                if ($childcount == $grandchildcount) {
                    $type = 'controller';
                } else {
                    $type = 'plugin';
                }
            }

            if ($type == 'plugin') {
                $plugin = $alias; $controller = false; $action = false;
            } else {
                $c = substr_count($alias, '/');
                $aliasE = explode('/', $alias);
                if ($type == 'action') {
                    if ($c == 2) {
                        $plugin = $aliasE[0];
                        $controller = $aliasE[1];
                        $action = $aliasE[2];
                    } elseif ($c == 1) {
                        $plugin = false;
                        $controller = $aliasE[0];
                        $action = $aliasE[1];
                    }
                } elseif ($type == 'controller') {
                    if ($c == 1) {
                        $plugin = $aliasE[0];
                        $controller = $aliasE[1];
                        $action = '';
                    } elseif ($c == 0) {
                        $plugin = false;
                        $controller = $aliasE[0];
                        $action = '';
                    }
                }
            }
            $acoPaths[$id] = array(
                'Aco' => array(
                    'type' => $type,
                    'alias' => $alias,
                    'children' => $childcount,
                    'plugin' => $plugin,
                    'controller' => $controller,
                    'action' => $action,
                ));
        }
        $this->set(compact('acoPaths'));

        $rolesAros = $this->AclAro->find('all', array(
            'conditions' => array(
                'AclAro.model' => 'Role',
                'AclAro.foreign_key' => array_keys($roles),
            ),
        ));
        $rolesAros = Set::combine($rolesAros, '{n}.AclAro.foreign_key', '{n}.AclAro.id');

        $permissions = array(); // acoId => roleId => bool
        foreach ($acos AS $acoId => $acoAlias) {
            if (substr_count($acoAlias, '-') != 0) {
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