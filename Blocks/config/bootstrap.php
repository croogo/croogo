<?php

use Cake\Core\Configure;
use Croogo\Croogo\Cache\CroogoCache;
use Croogo\Croogo\Croogo;

CroogoCache::config('croogo_blocks', array_merge(
	Configure::read('Croogo.Cache.defaultConfig'),
	array('groups' => array('blocks'))
));

Croogo::hookComponent('*', array(
	'Croogo/Blocks.Blocks' => array(
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
