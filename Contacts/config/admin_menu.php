<?php

namespace Croogo\Contacts\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'contacts', array(
	'icon' => 'comments',
	'title' => __d('croogo', 'Contacts'),
	'url' => array(
		'admin' => true,
		'plugin' => 'Croogo/Contacts',
		'controller' => 'Contacts',
		'action' => 'index',
	),
	'weight' => 50,
	'children' => array(
		'contacts' => array(
			'title' => __d('croogo', 'Contacts'),
			'url' => array(
				'admin' => true,
				'plugin' => 'Croogo/Contacts',
				'controller' => 'Contacts',
				'action' => 'index',
			),
		),
		'messages' => array(
			'title' => __d('croogo', 'Messages'),
			'url' => array(
				'admin' => true,
				'plugin' => 'Croogo/Contacts',
				'controller' => 'Messages',
				'action' => 'index',
			),
		),
	),
));
