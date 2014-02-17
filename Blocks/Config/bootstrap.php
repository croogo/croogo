<?php

CroogoCache::config('croogo_blocks', array_merge(
	Configure::read('Cache.defaultConfig'),
	array('groups' => array('blocks'))
));

Croogo::hookComponent('*', array(
	'Blocks.Blocks' => array(
		'priority' => 5,
	)
));

Croogo::hookHelper('*', 'Blocks.Regions');

Croogo::mergeConfig('Translate.models.Block', array(
	'fields' => array(
		'title' => 'titleTranslation',
		'body' => 'bodyTranslation',
	),
	'translateModel' => 'Blocks.Block',
));
