<?php

CroogoCache::config('contacts_view', array_merge(
	Configure::read('Cache.defaultConfig'),
	array('groups' => array('contacts'))
));

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

