<?php

namespace Croogo\Taxonomy\Config;
CroogoNav::add('sidebar', 'content.children.content_types', array(
	'title' => __d('croogo', 'Content Types'),
	'url' => array(
		'admin' => true,
		'plugin' => 'taxonomy',
		'controller' => 'types',
		'action' => 'index',
	),
	'weight' => 30,
));

CroogoNav::add('sidebar', 'content.children.taxonomy', array(
	'title' => __d('croogo', 'Taxonomy'),
	'url' => array(
		'admin' => true,
		'plugin' => 'taxonomy',
		'controller' => 'vocabularies',
		'action' => 'index',
	),
	'weight' => 40,
	'children' => array(
		'list' => array(
			'title' => __d('croogo', 'List'),
			'url' => array(
				'admin' => true,
				'plugin' => 'taxonomy',
				'controller' => 'vocabularies',
				'action' => 'index',
			),
			'weight' => 10,
		),
		'add_new' => array(
			'title' => __d('croogo', 'Add new'),
			'url' => array(
				'admin' => true,
				'plugin' => 'taxonomy',
				'controller' => 'vocabularies',
				'action' => 'add',
			),
			'weight' => 20,
			'htmlAttributes' => array('class' => 'separator'),
		)
	)
));

