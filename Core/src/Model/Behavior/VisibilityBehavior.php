<?php
declare(strict_types=1);

namespace Croogo\Core\Model\Behavior;

use Cake\Database\Query;
use Cake\ORM\Behavior;

class VisibilityBehavior extends Behavior
{

    public function findByAccess(Query $query, array $options = [])
    {
        $options += ['roleId' => null];
        $visibilityRolesField = $this->_table->aliasField('visibility_roles');

        return $query->andWhere([
            'OR' => [
                $visibilityRolesField => $query->expr()
                    ->add('\'\''),
                $visibilityRolesField . ' IS NULL',
                $visibilityRolesField . ' LIKE' => '%"' . $options['roleId'] . '"%',
            ],
        ]);
    }
}
