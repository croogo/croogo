<?php

function find_root() {
	$root = getcwd();

	while (!is_dir($root . '/vendor/cakephp/cakephp')) {
		$root = dirname($root);
	}

	return $root;
}

function find_app() {
	if (is_dir(ROOT . '/App')) {
		return 'App';
	}

	if (is_dir(ROOT . '/vendor/cakephp/app/App')) {
		return 'vendor/cakephp/app/App';
	}

	if (is_dir(dirname(__DIR__) . '/Croogo/tests/test_app')) {
		return dirname(__DIR__) . '/Croogo/tests/test_app';
	}
}

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
define('ROOT', find_root());
define('APP_DIR', find_app());
define('WEBROOT_DIR', 'webroot');
define('CONFIG', APP_DIR . DS . 'config' . DS);
define('APP', APP_DIR . DS);
define('WWW_ROOT', ROOT . DS . WEBROOT_DIR . DS);
define('TESTS', ROOT . DS . 'Test' . DS);
define('TMP', ROOT . DS . 'tmp' . DS);
define('LOGS', TMP . 'logs' . DS);
define('CACHE', TMP . 'cache' . DS);
define('CAKE_CORE_INCLUDE_PATH', ROOT . '/vendor/cakephp/cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . 'src' . DS);

require ROOT . DS . 'vendor' . DS . 'autoload.php';
require CORE_PATH . 'config' . DS . 'bootstrap.php';

Cake\Core\Configure::write('App', [
	'namespace' => 'App',
	'paths' => [
		'plugins' => [
			APP . 'plugins' . DS,
		],
		'templates' => [APP . 'Template' . DS],
		'locales' => [APP . 'Locale' . DS],
	],
]);
Cake\Core\Configure::write('debug', 2);

$Tmp = new \Cake\Filesystem\Folder(TMP);
$Tmp->create(TMP . 'cache/models', 0777);
$Tmp->create(TMP . 'cache/persistent', 0777);
$Tmp->create(TMP . 'cache/views', 0777);

$cache = [
	'default' => [
		'engine' => 'File',
		'path' => CACHE
	],
	'_cake_core_' => [
		'className' => 'File',
		'prefix' => 'search_myapp_cake_core_',
		'path' => CACHE . 'persistent/',
		'serialize' => true,
		'duration' => '+10 seconds'
	],
	'_cake_model_' => [
		'className' => 'File',
		'prefix' => 'search_my_app_cake_model_',
		'path' => CACHE . 'models/',
		'serialize' => 'File',
		'duration' => '+10 seconds'
	]
];

Cake\Cache\Cache::config($cache);
Cake\Core\Plugin::load('Croogo', ['bootstrap' => true, 'autoload' => true, 'path' => __DIR__ . DS . '..' . DS . '..' . DS]);
Cake\Core\Plugin::load('Croogo/Core', ['bootstrap' => true, 'routes' => true, 'autoload' => true]);
Cake\Core\Plugin::load('Croogo/Users', ['bootstrap' => true, 'routes' => true, 'autoload' => true]);
Cake\Core\Plugin::load('Croogo/Nodes', ['bootstrap' => true, 'routes' => true, 'autoload' => true]);
Cake\Core\Plugin::load('Croogo/Settings', ['bootstrap' => true, 'routes' => true, 'autoload' => true]);

// Ensure default test connection is defined
if (!getenv('db_class')) {
	putenv('db_class=Cake\Database\Driver\Sqlite');
	putenv('db_dsn=sqlite::memory:');
}
Cake\Datasource\ConnectionManager::config('test', [
	'className' => 'Cake\Database\Connection',
	'driver' => getenv('db_class'),
	'dsn' => getenv('db_dsn'),
	'database' => getenv('db_database'),
	'username' => getenv('db_login'),
	'password' => getenv('db_password'),
	'timezone' => 'UTC'
]);
