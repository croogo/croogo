<?php

$cacheConfig = array_merge(
	Configure::read('Cache.defaultConfig'),
	array('groups' => array('taxonomy'))
);
CroogoCache::config('croogo_types', $cacheConfig);
CroogoCache::config('croogo_vocabularies', $cacheConfig);

if (CakePlugin::loaded('Nodes')) {
	Croogo::hookModelProperty('Taxonomy', 'hasAndBelongsToMany', array(
		'Node' => array(
			'className' => 'Nodes.Node',
		),
	));
}

Croogo::hookComponent('*', 'Taxonomy.Taxonomies');

Croogo::hookHelper('*', 'Taxonomy.Taxonomies');

CroogoNav::add('content.children.content_types', array(
	'title' => __d('croogo', 'Content Types'),
	'url' => array(
		'admin' => true,
		'plugin' => 'taxonomy',
		'controller' => 'types',
		'action' => 'index',
	),
	'weight' => 30,
));

CroogoNav::add('content.children.taxonomy', array(
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
