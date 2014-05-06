<?php

$cacheConfig = array_merge(
	Configure::read('Cache.defaultConfig'),
	array('groups' => array('nodes'))
);
CroogoCache::config('nodes', $cacheConfig);
CroogoCache::config('nodes_view', $cacheConfig);
CroogoCache::config('nodes_promoted', $cacheConfig);
CroogoCache::config('nodes_term', $cacheConfig);
CroogoCache::config('nodes_index', $cacheConfig);

Croogo::hookApiComponent('Nodes', 'Nodes.NodeApi');
Croogo::hookComponent('*', 'Nodes.Nodes');

Croogo::hookHelper('*', 'Nodes.Nodes');

// Configure Wysiwyg
Croogo::mergeConfig('Wysiwyg.actions', array(
	'Nodes/admin_add' => array(
		array(
			'elements' => 'NodeBody',
		),
	),
	'Nodes/admin_edit' => array(
		array(
			'elements' => 'NodeBody',
		),
	),
	'Translate/admin_edit' => array(
		array(
			'elements' => 'NodeBody',
		),
	),
));

Croogo::mergeConfig('Translate.models.Node', array(
	'fields' => array(
		'title' => 'titleTranslation',
		'excerpt' => 'excerptTranslation',
		'body' => 'bodyTranslation',
	),
	'translateModel' => 'Nodes.Node',
));
