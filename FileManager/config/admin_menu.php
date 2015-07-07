<?php

namespace Croogo\FileManager\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'media', array(
	'icon' => 'picture',
	'title' => __d('croogo', 'Media'),
	'url' => array(
		'admin' => true,
		'plugin' => 'Croogo/FileManager',
		'controller' => 'Attachments',
		'action' => 'index',
	),
	'weight' => 40,
	'children' => array(
		'attachments' => array(
			'title' => __d('croogo', 'Attachments'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/FileManager',
				'controller' => 'Attachments',
				'action' => 'index',
			),
		),
		'file_manager' => array(
			'title' => __d('croogo', 'File Manager'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/FileManager',
				'controller' => 'FileManager',
				'action' => 'browse',
			),
		),
	),
));
