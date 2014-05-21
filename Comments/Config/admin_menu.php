<?php

namespace Croogo\Comments\Config;
CroogoNav::add('sidebar', 'content.children.comments', array(
	'title' => __d('croogo', 'Comments'),
	'url' => array(
		'admin' => true,
		'plugin' => 'comments',
		'controller' => 'comments',
		'action' => 'index',
	),
	'children' => array(
		'published' => array(
			'title' => __d('croogo', 'Published'),
			'url' => array(
				'admin' => true,
				'plugin' => 'comments',
				'controller' => 'comments',
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
				'plugin' => 'comments',
				'controller' => 'comments',
				'action' => 'index',
				'?' => array(
					'status' => '0',
				),
			),
		),
	),
));
