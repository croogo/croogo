<?php

CroogoNav::add('contacts', array(
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
		'attachments' => array(
			'title' => __d('croogo', 'Contacts'),
			'url' => array(
				'admin' => true,
				'plugin' => 'contacts',
				'controller' => 'contacts',
				'action' => 'index',
			),
		),
		'file_manager' => array(
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

