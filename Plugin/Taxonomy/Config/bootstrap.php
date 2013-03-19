<?php

Croogo::hookComponent('*', 'Taxonomy.Taxonomies');

Croogo::hookHelper('*', 'Taxonomy.Taxonomies');

CroogoNav::add('content.children.content_types', array(
	'title' => __('Content Types'),
	'url' => array(
		'plugin' => 'taxonomy',
		'admin' => true,
		'controller' => 'types',
		'action' => 'index',
	),
	'weight' => 30,
));

CroogoNav::add('content.children.taxonomy', array(
	'title' => __('Taxonomy'),
	'url' => array(
		'plugin' => 'taxonomy',
		'admin' => true,
		'controller' => 'vocabularies',
		'action' => 'index',
	),
	'weight' => 40,
	'children' => array(
		'list' => array(
			'title' => __('List'),
				'url' => array(
				'plugin' => 'taxonomy',
				'admin' => true,
				'controller' => 'vocabularies',
				'action' => 'index',
			),
			'weight' => 10,
		),
		'add_new' => array(
			'title' => __('Add new'),
			'url' => array(
				'plugin' => 'taxonomy',
				'admin' => true,
				'controller' => 'vocabularies',
				'action' => 'add',
			),
			'weight' => 20,
			'htmlAttributes' => array('class' => 'separator'),
		)
	)
));
