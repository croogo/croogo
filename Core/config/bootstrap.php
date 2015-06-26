<?php

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Type;
use Croogo\Core\Croogo;

// Map our custom types
Type::map('params', 'Croogo\Croogo\Database\Type\ParamsType');
Type::map('encoded', 'Croogo\Croogo\Database\Type\EncodedType');
Type::map('link', 'Croogo\Croogo\Database\Type\LinkType');

require_once 'croogo_bootstrap.php';

if (Configure::read('Croogo.installed')) {
	return;
}

// Load Install plugin
if (Configure::read('Security.salt') == 'f78b12a5c38e9e5c6ae6fbd0ff1f46c77a1e3' ||
	Configure::read('Security.cipherSeed') == '60170779348589376') {
	$_securedInstall = false;
}
Configure::write('Install.secured', !isset($_securedInstall));
Configure::write('Croogo.installed',
	file_exists(APP . 'config' . DS . 'database.php') &&
	file_exists(APP . 'config' . DS . 'settings.json') &&
	file_exists(APP . 'config' . DS . 'croogo.php')
);
if (!Configure::read('Croogo.installed') || !Configure::read('Install.secured')) {
	Plugin::load('Croogo/Install', ['routes' => true, 'path' => Plugin::path('Croogo/Croogo') . '..' . DS . 'Install' . DS]);
}
