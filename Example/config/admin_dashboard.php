<?php

use Croogo\Dashboards\CroogoDashboard;

return  [
    'example.welcome' => [
        'title' => __d('croogo', 'Welcome'),
        'cell' => 'Croogo/Example.ExampleDashboard::welcome',
        'weight' => 1,
        'column' => CroogoDashboard::FULL,
    ],
];
