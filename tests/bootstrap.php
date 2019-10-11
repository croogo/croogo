<?php
// @codingStandardsIgnoreFile

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Routing\DispatcherFactory;
use Cake\Filesystem\Folder;
use Croogo\Core\PluginManager;
use Croogo\Core\Test\Fixture\SettingsFixture;

$findVendor = function () {
    $root = dirname(__DIR__);
    if (is_dir($root . '/vendor/cakephp/cakephp')) {
        return $root . DS. 'vendor' . DS;
    }

    $root = dirname(dirname(dirname(dirname(__DIR__))));
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
require dirname(__DIR__) . DS . 'tests' . DS . 'test_app' . DS . 'config' . DS . '/paths.php';

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

date_default_timezone_set('UTC');

Configure::write('App', [
    'namespace' => 'App',
    'paths' => [
        'plugins' => [ROOT . DS . 'plugins' . DS],
        'templates' => [APP . 'Template' . DS],
        'locales' => [APP . 'Locale' . DS],
    ]
]);
Configure::write('debug', true);

$tmpDirectory = new Folder(TMP);
$tmpDirectory->delete(TMP . 'cache');
$tmpDirectory->create(TMP . 'cache/models', 0777);
$tmpDirectory->create(TMP . 'cache/persistent', 0777);
$tmpDirectory->create(TMP . 'cache/views', 0777);

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
    ],
    'cached_settings' => [
        'engine' => 'File',
        'groups' => ['settings'],
    ],
];
Cake\Cache\Cache::setConfig($cache);
Configure::write('Session', [
    'defaults' => 'php'
]);

// Ensure default test connection is defined
if (!getenv('db_class')) {
    putenv('db_class=Cake\Database\Driver\Sqlite');
    putenv('db_dsn=sqlite::memory:');
}
ConnectionManager::setConfig('test', [
    'className' => 'Cake\Database\Connection',
    'driver' => getenv('db_class'),
    'dsn' => getenv('db_dsn'),
    'database' => getenv('db_database'),
    'username' => getenv('db_login'),
    'password' => getenv('db_password'),
    'timezone' => 'UTC'
]);
ConnectionManager::setConfig('test_migrations', [
    'className' => 'Cake\Database\Connection',
    'driver' => getenv('db_class'),
    'dsn' => getenv('db_dsn'),
    'database' => getenv('db_database'),
    'username' => getenv('db_login'),
    'password' => getenv('db_password'),
    'timezone' => 'UTC'
]);

$settingsFixture = new SettingsFixture();

ConnectionManager::alias('test', 'default');
Configure::write('Acl.database', 'default');
$settingsFixture->create(ConnectionManager::get('default'));
$settingsFixture->insert(ConnectionManager::get('default'));

PluginManager::load('Croogo/Core', ['bootstrap' => true, 'routes' => true]);
PluginManager::load('Croogo/Settings', ['bootstrap' => true, 'routes' => true]);

DispatcherFactory::add('Routing');
DispatcherFactory::add('ControllerFactory');

class_alias('Croogo\Core\TestSuite\TestCase', 'Croogo\Core\TestSuite\CroogoTestCase');
