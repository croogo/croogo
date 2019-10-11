<?php

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Croogo\Core\Croogo;

Cache::setConfig('croogo_comments', array_merge(
    Configure::read('Croogo.Cache.defaultConfig'),
    ['groups' => ['comments']]
));

Croogo::hookHelper('*', 'Croogo/Comments.Comments');

Croogo::hookBehavior('Croogo/Nodes.Nodes', 'Croogo/Comments.Commentable');
