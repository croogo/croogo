<?php

use Croogo\Dashboards\CroogoDashboard;

$config = array(
	'dashboards.blogfeed' => array(
		'title' => __d('croogo', 'Croogo News'),
		'element' => 'Croogo/Dashboards.dashboard/blog_feed',
		'column' => CroogoDashboard::RIGHT,
	),
);
