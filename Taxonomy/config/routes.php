<?php

use Croogo\Core\CroogoRouter;

CroogoRouter::connect('/admin/terms/:action/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Taxonomy', 'controller' => 'Terms',
]);
CroogoRouter::connect('/admin/types/:action/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Taxonomy', 'controller' => 'Types',
]);
CroogoRouter::connect('/admin/vocabularies/:action/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Taxonomy', 'controller' => 'Vocabularies',
]);
