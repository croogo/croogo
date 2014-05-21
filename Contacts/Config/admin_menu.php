<?php

namespace Croogo\Contacts\Config;
CroogoNav::add('sidebar', 'contacts', array(
	'icon' => array('comments', 'large'),
	'title' => __d('croogo', 'Contacts'),
	'url' => array(
		'admin' => true,
		'plugin' => 'contacts',
		'controller' => 'contacts',
		'action' => 'index',
	),
	'weight' => 50,
	'children' => array(
		'contacts' => array(
			'title' => __d('croogo', 'Contacts'),
			'url' => array(
				'admin' => true,
				'plugin' => 'contacts',
				'controller' => 'contacts',
				'action' => 'index',
			),
		),
		'messages' => array(
			'title' => __d('croogo', 'Messages'),
			'url' => array(
				'admin' => true,
				'plugin' => 'contacts',
				'controller' => 'messages',
				'action' => 'index',
			),
		),
	),
));
