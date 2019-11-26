<?php

namespace Croogo\Users\Model\Table;

use Cake\Core\Exception\Exception;
use Croogo\Core\Model\Table\CroogoTable;

/**
 * RolesUsers
 *
 * @category Model
 * @package  Croogo.Users.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class RolesUsersTable extends CroogoTable
{

    public function initialize(array $config)
    {
        $this->belongsTo('Users', [
            'className' => 'Croogo/Users.Users',
        ]);
        $this->belongsTo('Roles', [
            'className' => 'Croogo/Users.Roles',
        ]);

        $this->addBehavior('Croogo/Core.Trackable');
    }

    /**
     * Get Ids of Role's Aro assigned to user
     *
     * @param $userId integer user id
     * @return array array of Role Aro Ids
     */
    public function getRolesAro($userId)
    {
        $rolesUsers = $this->find('all', [
            'fields' => 'role_id',
            'conditions' => [
                $this->aliasField('user_id') => $userId,
            ],
            'cache' => [
                'name' => 'user_roles_' . $userId,
                'config' => 'nodes_index',
            ],
        ]);
        $aroIds = [];
        foreach ($rolesUsers as $rolesUser) {
            try {
                $aro = $this->Roles->Aros->node([
                    'model' => 'Roles',
                    'foreign_key' => $rolesUser->role_id,
                ])->first();
                $aroIds[] = $aro->id;
            } catch (Exception $e) {
                continue;
            }
        }

        return $aroIds;
    }
}
