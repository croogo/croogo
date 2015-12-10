<?php

use Croogo\Core\CroogoRouter;

CroogoRouter::connect('/admin/link-chooser/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Core',
    'controller' => 'LinkChooser',
    'action' => 'linkChooser'
]);
