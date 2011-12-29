<?php

CroogoNav::add('content', array(
	'title' => __('Content'),
	'url' => array(
		'plugin' => false,
		'admin' => true,
		'controller' => 'nodes',
		'action' => 'index',
		),
	'access' => array('admin'),
	'weight' => 10,
	'children' => array(

		'list' => array(
			'title' => __('List'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'nodes',
				'action' => 'index',
				),
			'access' => array('admin'),
			'weight' => 10,
			),

		'create' => array(
			'title' => __('Create'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'nodes',
				'action' => 'create',
				),
			'access' => array('admin'),
			'weight' => 20,
			),

		'content_types' => array(
			'title' => __('Content Types'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'types',
				'action' => 'index',
				),
			'access' => array('admin'),
			'weight' => 30,
			),

		'taxonomy' => array(
			'title' => __('Taxonomy'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'vocabularies',
				'action' => 'index',
				),
			'access' => array('admin'),
			'weight' => 40,
			'children' => array(
				'list' => array(
					'title' => __('List'),
					'url' => array(
						'plugin' => false,
						'admin' => true,
						'controller' => 'vocabularies',
						'action' => 'index',
						),
					'access' => array('admin'),
					'weight' => 10,
					),
				'add_new' => array(
					'title' => __('Add new'),
					'url' => array(
						'plugin' => false,
						'admin' => true,
						'controller' => 'vocabularies',
						'action' => 'add',
						),
					'access' => array('admin'),
					'weight' => 20,
					'htmlAttributes' => array('class' => 'separator'),
					),
				),
			),

		'comments' => array(
			'title' => __('Comments'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'comments',
				'action' => 'index',
				),
			'access' => array('admin'),
			'children' => array(
				'published' => array(
					'title' => __('Published'),
					'url' => array(
						'plugin' => false,
						'admin' => true,
						'controller' => 'comments',
						'action' => 'index',
						'filter' => 'status:1;',
						),
					'access' => array('admin'),
					),
				'approval' => array(
					'title' => __('Approval'),
					'url' => array(
						'plugin' => false,
						'admin' => true,
						'controller' => 'comments',
						'action' => 'index',
						'filter' => 'status:0;',
						),
					'access' => array('admin'),
					),
				),
			),
		),

	));


CroogoNav::add('menus', array(
	'title' => __('Menus'),
	'url' => array(
		'plugin' => false,
		'admin' => true,
		'controller' => 'menus',
		'action' => 'index',
		),
	'access' => array('admin'),
	'weight' => 20,
	'children' => array(
		'menus' => array(
			'title' => __('Menus'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'menus',
				'action' => 'index',
				),
			'access' => array('admin'),
			'weight' => 10,
			),
		'add_new' => array(
			'title' => __('Add new'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'menus',
				'action' => 'add',
				),
			'access' => array('admin'),
			'weight' => 20,
			'htmlAttributes' => array('class' => 'separator'),
			),
		),
	));

CroogoNav::add('blocks', array(
	'title' => __('Blocks'),
	'url' => array(
		'plugin' => false,
		'admin' => true,
		'controller' => 'blocks',
		'action' => 'index',
		),
	'access' => array('admin'),
	'weight' => 30,
	'children' => array(
		'blocks' => array(
			'title' => __('Blocks'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'blocks',
				'action' => 'index',
				),
			'access' => array('admin'),
			),
		'regions' => array(
			'title' => __('Regions'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'regions',
				'action' => 'index',
				),
			'access' => array('admin'),
			),
		),
	));

CroogoNav::add('media', array(
	'title' => __('Media'),
	'url' => array(
		'plugin' => false,
		'admin' => true,
		'controller' => 'attachments',
		'action' => 'index',
		),
	'access' => array('admin'),
	'weight' => 40,
	'children' => array(
		'attachments' => array(
			'title' => __('Attachments'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'attachments',
				'action' => 'index',
				),
			'access' => array('admin'),
			),
		'file_manager' => array(
			'title' => __('File Manager'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'filemanager',
				'action' => 'index',
				),
			'access' => array('admin'),
			),
		),
	));

CroogoNav::add('contacts', array(
	'title' => __('Contacts'),
	'url' => array(
		'plugin' => false,
		'admin' => true,
		'controller' => 'contacts',
		'action' => 'index',
		),
	'access' => array('admin'),
	'weight' => 50,
	'children' => array(
		'attachments' => array(
			'title' => __('Contacts'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'contacts',
				'action' => 'index',
				),
			'access' => array('admin'),
			),
		'file_manager' => array(
			'title' => __('Messages'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'messages',
				'action' => 'index',
				),
			'access' => array('admin'),
			),
		),
	));

CroogoNav::add('users', array(
	'title' => __('Users'),
	'url' => array(
		'plugin' => false,
		'admin' => true,
		'controller' => 'users',
		'action' => 'index',
		),
	'access' => array('admin'),
	'weight' => 50,
	'children' => array(
		'users' => array(
			'title' => __('Users'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'users',
				'action' => 'index',
				),
			'access' => array('admin'),
			'weight' => 10,
			),
		'roles' => array(
			'title' => __('Roles'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'roles',
				'action' => 'index',
				),
			'access' => array('admin'),
			'weight' => 20,
			),
		),
	));

CroogoNav::add('settings', array(
	'title' => __('Settings'),
	'url' => array(
		'plugin' => false,
		'admin' => true,
		'controller' => 'settings',
		'action' => 'prefix',
		'Site',
		),
	'access' => array('admin'),
	'weight' => 60,
	'children' => array(
		'site' => array(
			'title' => __('Site'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'settings',
				'action' => 'prefix',
				'Site',
				),
			'access' => array('admin'),
			'weight' => 10,
			),

		'meta' => array(
			'title' => __('Meta'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'settings',
				'action' => 'prefix',
				'Meta',
				),
			'access' => array('admin'),
			'weight' => 20,
			),

		'reading' => array(
			'title' => __('Reading'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'settings',
				'action' => 'prefix',
				'Reading',
				),
			'access' => array('admin'),
			'weight' => 30,
			),

		'writing' => array(
			'title' => __('Writing'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'settings',
				'action' => 'prefix',
				'Writing',
				),
			'access' => array('admin'),
			'weight' => 40,
			),

		'comment' => array(
			'title' => __('Comment'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'settings',
				'action' => 'prefix',
				'Comment',
				),
			'access' => array('admin'),
			'weight' => 50,
			),

		'service' => array(
			'title' => __('Service'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'settings',
				'action' => 'prefix',
				'Service',
				),
			'access' => array('admin'),
			'weight' => 60,
			),

		'languages' => array(
			'title' => __('Languages'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'languages',
				'action' => 'index',
				),
			'access' => array('admin'),
			'weight' => 70,
			),

		),
	));
