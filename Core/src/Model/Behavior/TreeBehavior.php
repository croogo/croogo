<?php

namespace Croogo\Core\Model\Behavior;

use Cake\ORM\Behavior\TreeBehavior as CakeTree;
use Cake\Event\Event;
use Cake\ORM\Query;

/**
 * This class applies configured scope for normal find() and paginate() calls
 *
 * @see Cake\ORM\Behavior\TreeBehavior
 */
class TreeBehavior extends CakeTree
{

    public function beforeFind(Event $event, Query $query, $options)
    {
        $scope = $this->config('scope');
        if ($scope) {
            $this->_scope($query);
        }
        return $query;
    }
}
