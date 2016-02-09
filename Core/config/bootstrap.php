<?php

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Type;
use Croogo\Core\Croogo;

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
Croogo::hookComponent('*', 'Acl.Acl');
Croogo::hookComponent('*', 'Auth');
Croogo::hookComponent('*', 'Flash');
Croogo::hookComponent('*', 'RequestHandler');
Croogo::hookComponent('*', 'Croogo/Core.Theme');

require_once 'croogo_bootstrap.php';

Croogo::hookHelper('*', 'Croogo/Core.Js');
Croogo::hookHelper('*', 'Croogo/Core.Layout');
Croogo::hookHelper('*', 'Croogo/Core.CroogoApp');

if (Configure::read('Croogo.installed')) {
    return;
}

// Load Install plugin
if (Configure::read('Security.salt') == 'f78b12a5c38e9e5c6ae6fbd0ff1f46c77a1e3' ||
    Configure::read('Security.cipherSeed') == '60170779348589376') {
    $_securedInstall = false;
}
Configure::write('Install.secured', !isset($_securedInstall));
Configure::write(
    'Croogo.installed',
    file_exists(APP . 'config' . DS . 'database.php') &&
    file_exists(APP . 'config' . DS . 'croogo.php')
);
if (!Configure::read('Croogo.installed') || !Configure::read('Install.secured')) {
    Plugin::load('Croogo/Install', ['routes' => true, 'path' => Plugin::path('Croogo/Core') . '..' . DS . 'Install' . DS]);
}
