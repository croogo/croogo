<?php

namespace Croogo\Acl\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

/**
 * RowLevelAcl Behavior
 *
 * @package Croogo.Acl.Model.Behavior
 * @since 1.5
 */
class RowLevelAclBehavior extends Behavior
{

/**
 * afterSave
 *
 * Creates an ACO record for current model record, and give permissions to the
 * creator. If 'RolePermission' is present, 'grant' or 'inherit' permissions
 * for the role.
 */
    public function afterSave(Event $event)
    {
        $entity = $event->data['entity'];
        if (!$entity || !$entity->id) {
            return;
        }
        $Table = $event->subject();
        $aco = $Table->node($entity)->firstOrFail();
        $aco->alias = sprintf('%s.%s', $Table->alias(), $entity->id);
        $saved = $Table->Aco->save($aco);

        $user = $_SESSION['Auth']['User'];
        if ($entity->isNew() && !empty($user['id'])) {
            $aro = ['Users' => $user];
            $Permissions = TableRegistry::get('Permissions');
            $Permissions->allow($aro, $aco->alias);
        }

        /* FIXME
        if (!empty($model->data['RolePermission'])) {
            foreach ($model->data['RolePermission'] as $roleId => $checked) {
                $aro = ['model' => 'Role', 'foreign_key' => $roleId];
                $aco = ['model' => $model->alias, 'foreign_key' => $model->id];
                $model->Aco->Permission->allow($aro, $aco, '*', $checked ? 1 : 0);
            }
        }
        */
    }
}
