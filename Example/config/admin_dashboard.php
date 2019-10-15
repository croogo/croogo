<?php

use Croogo\Dashboards\CroogoDashboard;

$config = [
    'example.welcome' => [
        'title' => __d('croogo', 'Welcome'),
        'cell' => 'Croogo/Example.ExampleDashboard::welcome',
        'weight' => 1,
        'column' => CroogoDashboard::FULL,
    ],
];
