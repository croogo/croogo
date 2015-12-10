<?php

namespace Croogo\Core\Model\Behavior;

use Cake\Database\Query;
use Cake\ORM\Behavior;

class VisibilityBehavior extends Behavior
{

    public function findVisibilityRole(Query $query, array $options = [])
    {
        $query->where([
            'AND' => [
                [
                    'OR' => [
                        $this->_table->alias() . '.visibility_roles IS NULL',
                        $this->_table->alias() . '.visibility_roles LIKE' => '%"' . $options['role_id'] . '"%',
                    ],
                ],
            ],
        ]);

        return $query;
    }
}
