<?php

use Cake\Core\Configure;
use Croogo\Core\Cache\CroogoCache;
use Croogo\Core\Croogo;

CroogoCache::config('croogo_menus', array_merge(
	Configure::read('Croogo.Cache.defaultConfig'),
	array('groups' => array('menus'))
));

Croogo::hookComponent('*', 'Croogo/Menus.Menus');

Croogo::hookHelper('*', 'Croogo/Menus.Menus');

Croogo::mergeConfig('Translate.models.Link', array(
	'fields' => array(
		'title' => 'titleTranslation',
		'description' => 'descriptionTranslation',
	),
	'translateModel' => 'Croogo/Menus.Link',
));
