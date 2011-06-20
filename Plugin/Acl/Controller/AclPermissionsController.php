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
        $this->set('title_for_layout', __('Permissions', true));

        $acos = $this->AclFilter->acoTreelist();
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