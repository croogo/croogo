<?php

use Cake\Core\Configure;
use Cake\Routing\Router;
use Croogo\Core\Cache\CroogoCache;
use Croogo\Core\Croogo;

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
	'Croogo\\Nodes\\Controller\\Admin\\NodesController.add' => array(
		array(
			'elements' => 'NodeBody',
		),
	),
	'Croogo\\Nodes\\Controller\\Admin\\NodesController.edit' => array(
		array(
			'elements' => 'NodeBody',
		),
	),
	'Croogo\\Translate\\Controller\\Admin\\TranslateController.edit' => array(
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
