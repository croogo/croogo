<?php

CroogoCache::config('contacts_view', array_merge(
	Configure::read('Cache.defaultConfig'),
	array('groups' => array('contacts'))
));
