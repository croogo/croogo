<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class ThingsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Croogo/Core.Url', [
            'url' => [
                'controller' => 'Things',
                'action' => 'view',
            ],
            'pass' => [
                'id',
            ]
        ]);
    }
}
