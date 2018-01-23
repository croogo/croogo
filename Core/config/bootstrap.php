<?php

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Type;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Security;
use Croogo\Core\Croogo;

\Croogo\Core\timerStart('Croogo bootstrap');
$dbConfigExists = false;
$salted = Security::getSalt() !== '__SALT__';

if (file_exists(ROOT . DS . 'config' . DS . 'database.php')) {
    Configure::load('database', 'default');
    ConnectionManager::drop('default');
    ConnectionManager::config(Configure::consume('Datasources'));
}

try {
    $defaultConnection = ConnectionManager::get('default');
    $dbConfigExists = $defaultConnection->connect();
} catch (\Exception $e) {
    $dbConfigExists = false;
}

// Map our custom types
Type::map('params', 'Croogo\Core\Database\Type\ParamsType');
Type::map('encoded', 'Croogo\Core\Database\Type\EncodedType');
Type::map('link', 'Croogo\Core\Database\Type\LinkType');

Configure::write(
    'DebugKit.panels',
    array_merge((array)Configure::read('DebugKit.panels'), [
        'Croogo/Core.Plugins',
        'Croogo/Core.ViewHelpers',
        'Croogo/Core.Components',
    ])
);

Croogo::hookComponent('*', [
    'Croogo' => [
        'className' => 'Croogo/Core.Croogo',
        'priority' => 5
    ]
]);
Croogo::hookComponent('*', 'Croogo/Acl.Filter');
Croogo::hookComponent('*', 'Security');
Croogo::hookComponent('*', 'Csrf');
Croogo::hookComponent('*', 'Acl.Acl');
Croogo::hookComponent('*', 'Auth');
Croogo::hookComponent('*', 'Flash');
Croogo::hookComponent('*', 'RequestHandler');
Croogo::hookComponent('*', 'Croogo/Core.Theme');

require_once __DIR__ . DS . 'croogo_bootstrap.php';

Croogo::hookHelper('*', 'Croogo/Core.Js');
Croogo::hookHelper('*', 'Croogo/Core.Layout');
\Croogo\Core\timerStop('Croogo bootstrap');

// Load Install plugin
if (!Configure::read('Croogo.installed') || !$salted) {
    Plugin::load('Croogo/Install', ['routes' => true, 'bootstrap' => true]);
}
