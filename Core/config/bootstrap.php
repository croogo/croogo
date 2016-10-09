<?php

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Type;
use Croogo\Core\Croogo;

\Croogo\Core\timerStart('Croogo bootstrap');

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

\Croogo\Core\timerStop('Croogo bootstrap');

if (Configure::read('Croogo.installed')) {
    return;
}

// Load Install plugin
Configure::write(
    'Croogo.installed',
    file_exists(ROOT . 'config' . DS . 'database.php')
);
if (!Configure::read('Croogo.installed')) {
    Plugin::load('Croogo/Install', ['routes' => true]);
}
