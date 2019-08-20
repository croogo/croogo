<?php

use Croogo\Dashboards\CroogoDashboard;

return [
    'dashboards.blogfeed' => [
        'title' => __d('croogo', 'Croogo News'),
        'cell' => 'Croogo/Dashboards.BlogFeed::dashboard',
        'column' => CroogoDashboard::RIGHT,
        'access' => ['superadmin'],
    ],
];
