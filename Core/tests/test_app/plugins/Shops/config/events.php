<?php

namespace Croogo\Shops\Config;

$config = [
    'EventHandlers' => [
        'Shops.ShopsNodesEventHandler',
        'Shops.ShopsEventHandler' => [
            'options' => [
                'priority' => 1,
            ],
        ],
    ],
];
