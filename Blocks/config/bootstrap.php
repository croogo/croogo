<?php

use Cake\Core\Configure;
use Croogo\Core\Cache\CroogoCache;
use Croogo\Core\Croogo;

CroogoCache::config('croogo_blocks', array_merge(
	Configure::read('Croogo.Cache.defaultConfig'),
	array('groups' => array('blocks'))
));

Croogo::hookComponent('*', array(
	'BlocksHook' => array(
		'className' => 'Croogo/Blocks.Blocks',
		'priority' => 5,
	)
));

Croogo::hookHelper('*', 'Croogo/Blocks.Regions');

Croogo::mergeConfig('Translate.tables.Blocks', array(
	'fields' => array(
		'title' => 'titleTranslation',
		'body' => 'bodyTranslation',
	),
	'translateTable' => 'Croogo/Blocks.Blocks',
));
