<?php
// @codingStandardsIgnoreFile

use Cake\Core\Plugin;
use Cake\Routing\Router;

$findRoot = function () {
	$root = dirname(__DIR__);
	if (is_dir($root . '/vendor/cakephp/cakephp')) {
		return $root;
	}

	$root = dirname(dirname(__DIR__));
	if (is_dir($root . '/vendor/cakephp/cakephp')) {
		return $root;
	}

	$root = dirname(dirname(dirname(__DIR__)));
	if (is_dir($root . '/vendor/cakephp/cakephp')) {
		return $root;
	}
};

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
define('ROOT', $findRoot());
define('APP_DIR', 'test_app');
define('WEBROOT_DIR', 'webroot');
define('APP', ROOT . '/tests/test_app/');
define('CONFIG', ROOT . '/tests/test_app/config/');
define('WWW_ROOT', ROOT . DS . WEBROOT_DIR . DS);
define('TESTS', ROOT . DS . 'tests' . DS);
define('TMP', ROOT . DS . 'tmp' . DS);
define('LOGS', TMP . 'logs' . DS);
define('CACHE', TMP . 'cache' . DS);
define('CAKE_CORE_INCLUDE_PATH', ROOT . '/vendor/cakephp/cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . 'src' . DS);

require ROOT . '/vendor/autoload.php';
require CORE_PATH . 'config/bootstrap.php';

Cake\Core\Configure::write('App', [
	'namespace' => 'App',
	'paths' => [
		'plugins' => [APP . 'plugins' . DS],
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

\Cake\Core\Configure::write('plugins', [
    'Acl' => ROOT . DS . 'vendor' . DS . 'cakephp' . DS . 'acl' . DS,
    'BootstrapUI' => ROOT . DS . 'vendor' . DS .  'friendsofcake' . DS . 'boostrap-ui' . DS,
    'Croogo/Acl' => ROOT . DS . 'Acl' . DS,
    'Croogo/Blocks' => ROOT . DS . 'Blocks' . DS,
    'Croogo/Comments' => ROOT . DS . 'Comments' . DS,
    'Croogo/Contacts' => ROOT . DS . 'Contacts' . DS,
    'Croogo/Core' => ROOT . DS . 'Core' . DS,
    'Croogo/Dashboards' => ROOT . DS . 'Dashboards' . DS,
    'Croogo/Example' => ROOT . DS . 'Example' . DS,
    'Croogo/Extensions' => ROOT . DS . 'Extensions' . DS,
    'Croogo/FileManager' => ROOT . DS . 'FileManager' . DS,
    'Croogo/Install' => ROOT . DS . 'Install' . DS,
    'Croogo/Menus' => ROOT . DS . 'Menus' . DS,
    'Croogo/Meta' => ROOT . DS . 'Meta' . DS,
    'Croogo/Nodes' => ROOT . DS . 'Nodes' . DS,
    'Croogo/Settings' => ROOT . DS . 'Settings' . DS,
    'Croogo/Taxonomy' => ROOT . DS . 'Taxonomy' . DS,
    'Croogo/Translate' => ROOT . DS . 'Translate' . DS,
    'Croogo/Users' => ROOT . DS . 'Users' . DS,
    'Croogo/Wysiwyg' => ROOT . DS . 'Wysiwyg' . DS,
    'Migrations' => ROOT . DS . 'vendor' . DS . 'cakephp' . DS . 'migrations' . DS,
    'Search' => ROOT . DS . 'vendor' . DS . 'friendsofcake' . DS . 'search' . DS,
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
Plugin::load('Croogo/Core', ['bootstrap' => true, 'routes' => true, 'path' => ROOT . DS . 'Core' . DS]);

Cake\Routing\DispatcherFactory::add('Routing');
Cake\Routing\DispatcherFactory::add('ControllerFactory');

class_alias('Croogo\Core\TestSuite\TestCase', 'Croogo\Core\TestSuite\CroogoTestCase');

Plugin::routes();
