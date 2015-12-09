<?php

namespace Croogo\Core\Model\Behavior;

use Cake\ORM\Behavior;
use Croogo\Core\Croogo;

class AssociableBehavior extends Behavior
{

    public function initialize(array $config)
    {
        $this->_table->addAssociations(Croogo::options('Hook.table_properties', $this->_table, 'hookedAssociations'));
    }
}
