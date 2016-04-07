<?php

namespace Croogo\Shops\Config;

return [
    'EventHandlers' => [
        'Shops.ShopsNodesEventHandler',
        'Shops.ShopsEventHandler' => [
            'options' => [
                'priority' => 1,
            ],
        ],
    ],
];
