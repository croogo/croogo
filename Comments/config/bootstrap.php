<?php

use Cake\Core\Configure;
use Cake\Cache\Cache;
use Croogo\Core\Croogo;

Cache::config('croogo_comments', array_merge(
    Configure::read('Croogo.Cache.defaultConfig'),
    ['groups' => ['comments']]
));

Croogo::hookHelper('*', 'Croogo/Comments.Comments');

Croogo::hookBehavior('Croogo/Nodes.Nodes', 'Croogo/Comments.Commentable');
