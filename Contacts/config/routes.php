<?php

use Croogo\Core\CroogoRouter;

CroogoRouter::connect('/contact/*', [
    'plugin' => 'Croogo/Contacts', 'controller' => 'Contacts', 'action' => 'view',
]);

CroogoRouter::connect('/admin/contacts/contacts/:action/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Contacts', 'controller' => 'Contacts',
]);

CroogoRouter::connect('/admin/contacts/messages/:action/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Contacts', 'controller' => 'Messages',
]);
