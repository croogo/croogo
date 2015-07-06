<?php

use Croogo\Dashboards\CroogoDashboard;

$config = array(
	'dashboards.blogfeed' => array(
		'title' => __d('croogo', 'Croogo News'),
		'cell' => 'Croogo/Dashboards.BlogFeed::dashboard',
		'column' => CroogoDashboard::RIGHT,
	),
);
