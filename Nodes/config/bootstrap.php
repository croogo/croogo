<?php

use Cake\Core\Configure;
use Cake\Routing\Router;
use Croogo\Croogo\Cache\CroogoCache;
use Croogo\Croogo\Croogo;

$cacheConfig = array_merge(
	Configure::read('Croogo.Cache.defaultConfig'),
	array('groups' => array('nodes'))
);
CroogoCache::config('nodes', $cacheConfig);
CroogoCache::config('nodes_view', $cacheConfig);
CroogoCache::config('nodes_promoted', $cacheConfig);
CroogoCache::config('nodes_term', $cacheConfig);
CroogoCache::config('nodes_index', $cacheConfig);

Croogo::hookApiComponent('Nodes', 'Nodes.NodeApi');
Croogo::hookComponent('*', 'Croogo/Nodes.Nodes');

Croogo::hookHelper('*', 'Croogo/Nodes.Nodes');

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
