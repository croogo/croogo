<?php

namespace Croogo\Core\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior\TreeBehavior as CakeTree;
use Cake\ORM\Query;

/**
 * This class applies configured scope for normal find() and paginate() calls
 *
 * @see Cake\ORM\Behavior\TreeBehavior
 */
class TreeBehavior extends CakeTree
{

    public function beforeFind(\Cake\Event\EventInterface $event, \Cake\ORM\Query\SelectQuery $query, $options)
    {
        $scope = $this->getConfig('scope');
        if ($scope) {
            $this->_scope($query);
        }

        return $query;
    }
}
