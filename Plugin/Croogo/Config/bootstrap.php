<?php

App::uses('CakeLog', 'Log');
App::uses('CroogoPlugin', 'Extensions.Lib');
App::uses('CroogoEventManager', 'Croogo.Event');
App::uses('Croogo', 'Croogo.Lib');
App::uses('CroogoNav', 'Croogo.Lib');

CakePlugin::load(array('Extensions'), array('bootstrap' => true));
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
Configure::write('Install.installed',
	file_exists(APP . 'Config' . DS . 'database.php') &&
	file_exists(APP . 'Config' . DS . 'settings.json') &&
	file_exists(APP . 'Config' . DS . 'croogo.php')
);
if (!Configure::read('Install.installed') || !Configure::read('Install.secured')) {
	CakePlugin::load('Install', array('routes' => true));
}