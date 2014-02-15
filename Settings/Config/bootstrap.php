<?php

CroogoCache::config('cached_settings', array_merge(
	Configure::read('Cache.defaultConfig'),
	array('groups' => array('settings'))
));

Croogo::hookComponent('*', 'Settings.Settings');
