<?php

namespace Suppliers\Model\Behavior;

use Cake\ORM\Behavior;

class SuppliersOrderMonitorBehavior extends Behavior
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->_table->monitored = true;
    }
}
