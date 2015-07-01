<?php

namespace Croogo\Comments\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'content.children.comments', array(
	'title' => __d('croogo', 'Comments'),
	'url' => array(
		'admin' => true,
		'plugin' => 'Croogo/comments',
		'controller' => 'Comments',
		'action' => 'index',
	),
	'children' => array(
		'published' => array(
			'title' => __d('croogo', 'Published'),
			'url' => array(
				'admin' => true,
				'plugin' => 'Croogo/Comments',
				'controller' => 'Comments',
				'action' => 'index',
				'?' => array(
					'status' => '1',
				),
			),
		),
		'approval' => array(
			'title' => __d('croogo', 'Approval'),
			'url' => array(
				'admin' => true,
				'plugin' => 'Croogo/Comments',
				'controller' => 'Comments',
				'action' => 'index',
				'?' => array(
					'status' => '0',
				),
			),
		),
	),
));
