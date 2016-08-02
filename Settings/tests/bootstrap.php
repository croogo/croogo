<?php
// @codingStandardsIgnoreFile

use Cake\Routing\Router;
use Croogo\Core\Plugin;

$findVendor = function () {
    $root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
    if (is_dir($root . '/vendor/cakephp/cakephp')) {
        return $root . DS. 'vendor' . DS;
    }
};

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

define('VENDOR', $findVendor());

/**
 * Configure paths required to find CakePHP + general filepath
 * constants
 */
require dirname(dirname(__DIR__)) . DS . 'tests' . DS . 'test_app' . DS . 'config' . DS . '/paths.php';

// Use composer to load the autoloader.
require VENDOR . 'autoload.php';

/**
 * Bootstrap CakePHP.
 *
 * Does the various bits of setup that CakePHP needs to do.
 * This includes:
 *
 * - Registering the CakePHP autoloader.
 * - Setting the default application paths.
 */
require CORE_PATH . 'config' . DS . 'bootstrap.php';

Cake\Core\Configure::write('App', [
	'namespace' => 'App',
	'paths' => [
        'plugins' => [ROOT . DS . 'plugins' . DS],
        'templates' => [APP . 'Template' . DS],
        'locales' => [APP . 'Locale' . DS],
	]
]);
Cake\Core\Configure::write('debug', true);

$TMP = new \Cake\Filesystem\Folder(TMP);
$TMP->create(TMP . 'cache/models', 0777);
$TMP->create(TMP . 'cache/persistent', 0777);
$TMP->create(TMP . 'cache/views', 0777);

$cache = [
	'default' => [
		'engine' => 'File'
	],
	'_cake_core_' => [
		'className' => 'File',
		'prefix' => 'croogo_core_myapp_cake_core_',
		'path' => CACHE . 'persistent/',
		'serialize' => true,
		'duration' => '+10 seconds'
	],
	'_cake_model_' => [
		'className' => 'File',
		'prefix' => 'croogo_core_my_app_cake_model_',
		'path' => CACHE . 'models/',
		'serialize' => 'File',
		'duration' => '+10 seconds'
	]
];

Cake\Cache\Cache::config($cache);
Cake\Core\Configure::write('Session', [
	'defaults' => 'php'
]);

// Ensure default test connection is defined
if (!getenv('db_dsn')) {
    putenv('db_dsn=sqlite:///:memory:');
}

Cake\Datasource\ConnectionManager::config('test', [
    'url' => getenv('db_dsn'),
    'timezone' => 'UTC'
]);

$settingsFixture = new \Croogo\Core\Test\Fixture\SettingsFixture();

\Cake\Datasource\ConnectionManager::alias('test', 'default');
$settingsFixture->create(\Cake\Datasource\ConnectionManager::get('default'));
$settingsFixture->insert(\Cake\Datasource\ConnectionManager::get('default'));

Plugin::load('Croogo/Core', ['bootstrap' => true, 'routes' => true]);
Plugin::load('Croogo/Settings', ['bootstrap' => true, 'routes' => true]);

Cake\Routing\DispatcherFactory::add('Routing');
Cake\Routing\DispatcherFactory::add('ControllerFactory');

class_alias('Croogo\Core\TestSuite\TestCase', 'Croogo\Core\TestSuite\CroogoTestCase');

Plugin::routes();
