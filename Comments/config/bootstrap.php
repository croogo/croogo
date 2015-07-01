<?php

use Cake\Core\Configure;
use Croogo\Core\Cache\CroogoCache;
use Croogo\Core\Croogo;

CroogoCache::config('croogo_comments', array_merge(
	Configure::read('Cache.defaultConfig'),
	array('groups' => array('comments'))
));

Croogo::hookHelper('*', 'Croogo/Comments.Comments');
