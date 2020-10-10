<?php
declare(strict_types=1);

namespace Croogo\Acl\Model\Behavior;

use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;

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
    public function afterSave(EventInterface $event)
    {
        $entity = $event->getData('entity');
        if (!$entity || !$entity->id) {
            return;
        }
        $Table = $event->getSubject();
        $alias = $Table->getAlias();
        $aco = $Table->node($entity)->firstOrFail();
        $aco->alias = sprintf('%s.%s', $alias, $entity->id);
        $saved = $Table->Aco->save($aco);

        $user = $_SESSION['Auth']['User'];
        $Permissions = TableRegistry::getTableLocator()->get('Permissions');
        if ($entity->isNew() && !empty($user['id'])) {
            $aro = ['Users' => $user];
            $Permissions->allow($aro, $aco->alias);
        }

        if (!empty($entity->rolePermissions)) {
            foreach ($entity->rolePermissions as $roleId => $checked) {
                $aro = ['model' => 'Roles', 'foreign_key' => $roleId];
                $aco = ['model' => $alias, 'foreign_key' => $entity->id];
                $allowed = $checked ? 1 : 0;
                $Permissions->allow($aro, $aco, '*', $allowed);
            }
        }
    }
}
