<?php

use Croogo\Dashboards\CroogoDashboard;

$config = array(
	'core.blogfeed' => array(
		'title' => __d('croogo', 'Croogo News'),
		'cell' => 'Croogo/Core.BlogFeed::dashboard',
		'column' => CroogoDashboard::RIGHT,
	),
);
