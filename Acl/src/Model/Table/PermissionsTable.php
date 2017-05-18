<?php

namespace Croogo\Acl\Model\Table;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Utility\Hash;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

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
