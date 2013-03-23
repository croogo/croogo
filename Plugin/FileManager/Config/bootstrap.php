<?php

CroogoNav::add('media', array(
	'icon' => array('picture', 'large'),
	'title' => __d('croogo', 'Media'),
	'url' => array(
		'admin' => true,
		'plugin' => 'file_manager',
		'controller' => 'attachments',
		'action' => 'index',
	),
	'weight' => 40,
	'children' => array(
		'attachments' => array(
			'title' => __d('croogo', 'Attachments'),
			'url' => array(
				'admin' => true,
				'plugin' => 'file_manager',
				'controller' => 'attachments',
				'action' => 'index',
			),
		),
		'file_manager' => array(
			'title' => __d('croogo', 'File Manager'),
			'url' => array(
				'admin' => true,
				'plugin' => 'file_manager',
				'controller' => 'file_manager',
				'action' => 'browse',
			),
		),
	),
));
