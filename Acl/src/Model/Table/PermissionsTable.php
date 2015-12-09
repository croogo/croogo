<?php

namespace Croogo\Acl\Model\Table;

use Cake\Cache\Cache;
use Cake\Utility\Hash;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * AclPermission Model
 *
 * @category Model
 * @package  Croogo.Acl.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class PermissionsTable extends \Acl\Model\Table\PermissionsTable
{

/**
 * afterSave
 */
    public function afterSave($created, $options = [])
    {
        Cache::clearGroup('acl', 'permissions');
    }

/**
 * Generate allowed actions for current logged in Role
 *
 * @param int$roleId
 * @return array of elements formatted like ControllerName/action_name
 */
    public function getAllowedActionsByRoleId($roleId)
    {
        $aro = $this->Aro->node([
            'model' => 'Role',
            'foreign_key' => $roleId,
        ]);
        if (empty($aro[0]['Aro']['id'])) {
            return [];
        }
        $aroId = $aro[0]['Aro']['id'];

        $permissionsForCurrentRole = $this->find('list', [
            'conditions' => [
                'Permission.aro_id' => $aroId,
                'Permission._create' => 1,
                'Permission._read' => 1,
                'Permission._update' => 1,
                'Permission._delete' => 1,
            ],
            'fields' => [
                'Permission.id',
                'Permission.aco_id',
            ],
        ]);
        $permissionsByActions = [];
        foreach ($permissionsForCurrentRole as $acoId) {
            $path = $this->Aco->getPath($acoId);
            $path = join('/', Hash::extract($path, '{n}.Aco.alias'));
            $permissionsByActions[] = $path;
        }

        return $permissionsByActions;
    }

/**
 * Generate allowed actions for current logged in User
 *
 * @param int$userId
 * @return array of elements formatted like ControllerName/action_name
 */
    public function getAllowedActionsByUserId($userId)
    {
        $aro = $this->Aro->node([
            'model' => 'User',
            'foreign_key' => $userId,
        ]);
        if (empty($aro[0]['Aro']['id'])) {
            return [];
        }
        $aroIds = Hash::extract($aro, '{n}.Aro.id');
        if (Configure::read('Access Control.multiRole')) {
            $RolesUser = TableRegistry::get('Users.RolesUser');
            $rolesAro = $RolesUser->getRolesAro($userId);
            $aroIds = array_unique(Hash::merge($aroIds, $rolesAro));
        }

        $permissionsForCurrentUser = $this->find('list', [
            'conditions' => [
                'Permission.aro_id' => $aroIds,
                'Permission._create' => 1,
                'Permission._read' => 1,
                'Permission._update' => 1,
                'Permission._delete' => 1,
            ],
            'fields' => [
                'Permission.id',
                'Permission.aco_id',
            ],
        ]);
        $permissionsByActions = [];
        foreach ($permissionsForCurrentUser as $acoId) {
            $path = $this->Aco->getPath($acoId);
            if (!$path) {
                continue;
            }
            $path = join('/', Hash::extract($path, '{n}.Aco.alias'));
            if (!in_array($path, $permissionsByActions)) {
                $permissionsByActions[] = $path;
            }
        }

        return $permissionsByActions;
    }

/**
 * Retrieve an array for formatted aros/aco data
 *
 * @param array $acos
 * @param array $aros
 * @param array $options
 * @return array formatted array
 */
    public function format($acos, $aros, $options = [])
    {
        $options = Hash::merge([
            'model' => 'Roles',
            'perms' => true
        ], $options);
        extract($options);
        $permissions = [];

        foreach ($acos as $aco) {
            $acoId = $aco->id;
            $acoAlias = $aco->alias;

            $path = $this->Acos->find('path', ['for' => $acoId]);
            $path = join('/', collection($path)->extract('alias')->toArray());
            $data = [
                'children' => $this->Acos->childCount($aco, true),
                'depth' => substr_count($path, '/'),
            ];

            foreach ($aros as $aroFk => $aroId) {
                $role = [
                    'model' => $model, 'foreign_key' => $aroFk,
                ];
                if ($perms) {
                    if ($aroFk == 1 || $this->check($role, $path)) {
                        $data['roles'][$aroFk] = 1;
                    } else {
                        $data['roles'][$aroFk] = 0;
                    }
                }
                $permissions[$acoId] = [$acoAlias => $data];
            }

        }
        return $permissions;
    }
}
