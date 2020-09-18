<?php
declare(strict_types=1);

namespace Croogo\Core\Model\Behavior;

use ArrayObject;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior\TreeBehavior as CakeTree;
use Cake\ORM\Query;

/**
 * This class applies configured scope for normal find() and paginate() calls
 *
 * @see Cake\ORM\Behavior\TreeBehavior
 */
class TreeBehavior extends CakeTree
{

    public function beforeFind(EventInterface $event, Query $query, ArrayObject $options, bool $primary)
    {
        $scope = $this->setConfig('scope');
        if ($scope) {
            $this->_scope($query);
        }

        return $query;
    }
}
