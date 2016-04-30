<?php

use Cake\Core\Configure;
use Croogo\Core\Cache\CroogoCache;
use Croogo\Core\Croogo;

CroogoCache::config('croogo_comments', array_merge(
    Configure::read('Croogo.Cache.defaultConfig'),
    ['groups' => ['comments']]
));

Croogo::hookHelper('*', 'Croogo/Comments.Comments');

Croogo::hookBehavior('Croogo/Nodes.Nodes', 'Croogo/Comments.Commentable');
