<?php

namespace Croogo\Taxonomy\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'content.children.content_types', array(
	'title' => __d('croogo', 'Content Types'),
	'url' => array(
		'prefix' => 'admin',
		'plugin' => 'Croogo/Taxonomy',
		'controller' => 'Types',
		'action' => 'index',
	),
	'weight' => 30,
));

Nav::add('sidebar', 'content.children.taxonomy', array(
	'title' => __d('croogo', 'Taxonomy'),
	'url' => array(
		'prefix' => 'admin',
		'plugin' => 'Croogo/Taxonomy',
		'controller' => 'Vocabularies',
		'action' => 'index',
	),
	'weight' => 40,
	'children' => array(
		'list' => array(
			'title' => __d('croogo', 'List'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Taxonomy',
				'controller' => 'Vocabularies',
				'action' => 'index',
			),
			'weight' => 10,
		),
		'add_new' => array(
			'title' => __d('croogo', 'Add new'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Taxonomy',
				'controller' => 'Vocabularies',
				'action' => 'add',
			),
			'weight' => 20,
			'htmlAttributes' => array('class' => 'separator'),
		)
	)
));

