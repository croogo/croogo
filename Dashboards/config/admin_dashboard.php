<?php

use Croogo\Dashboards\CroogoDashboard;

$config = [
    'dashboards.blogfeed' => [
        'title' => __d('croogo', 'Croogo News'),
        'cell' => 'Croogo/Dashboards.BlogFeed::dashboard',
        'column' => CroogoDashboard::RIGHT,
        'access' => ['superadmin'],
    ],
];
