<?php

namespace Croogo\Extensions\Config;

$config = [
    'EventHandlers' => [
        'Croogo/Extensions.ExtensionsEventHandler' => [
            'options' => [
                'priority' => 5,
            ],
        ],
        'Croogo/Extensions.HookableComponentEventHandler' => [
            'options' => [
                'priority' => 5,
            ],
        ],
    ],
];
