<?php

use Cake\Core\Configure;
use Cake\Cache\Cache;
use Croogo\Core\Croogo;

$configured = Cache::configured();
if (!in_array('cached_settings', $configured)) {
	Cache::config('cached_settings', array_merge(
		Configure::read('Croogo.Cache.defaultConfig'),
		array('groups' => array('settings'))
	));
}

Croogo::hookComponent('*', [
	'SettingsComponent' => [
		'className' => 'Croogo/Settings.Settings'
	]
]);
