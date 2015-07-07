<?php

use Cake\Core\Configure;
use Croogo\Core\Cache\CroogoCache;
use Croogo\Core\Croogo;

CroogoCache::config('contacts_view', array_merge(
	Configure::read('Croogo.Cache.defaultConfig'),
	array('groups' => array('contacts'))
));

Croogo::mergeConfig('Translate.models.Contact', array(
	'fields' => array(
		'title' => 'titleTranslation',
		'body' => 'bodyTranslation',
	),
	'translateModel' => 'Contacts.Contact',
));
