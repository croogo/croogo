<?php

CroogoNav::add('contacts', array(
	'icon' => array('comments', 'large'),
	'title' => __('Contacts'),
	'url' => array(
		'admin' => true,
		'plugin' => 'contacts',
		'controller' => 'contacts',
		'action' => 'index',
	),
	'weight' => 50,
	'children' => array(
		'attachments' => array(
			'title' => __('Contacts'),
			'url' => array(
				'admin' => true,
				'plugin' => 'contacts',
				'controller' => 'contacts',
				'action' => 'index',
			),
		),
		'file_manager' => array(
			'title' => __('Messages'),
			'url' => array(
				'admin' => true,
				'plugin' => 'contacts',
				'controller' => 'messages',
				'action' => 'index',
			),
		),
	),
));

