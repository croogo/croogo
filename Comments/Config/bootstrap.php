<?php

use Cake\Core\Configure;
use Croogo\Croogo\Cache\CroogoCache;
use Croogo\Croogo\Croogo;

CroogoCache::config('croogo_comments', array_merge(
	Configure::read('Cache.defaultConfig'),
	array('groups' => array('comments'))
));

Croogo::hookHelper('*', 'Comments.Comments');
