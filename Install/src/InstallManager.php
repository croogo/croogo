<?php

namespace Croogo\Install;

use Cake\Cache\Cache;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Exception\MissingConnectionException;
use Cake\Datasource\ConnectionManager;
use Cake\Log\LogTrait;
use Cake\ORM\TableRegistry;
use Croogo\Acl\AclGenerator;
use Croogo\Core\Database\SequenceFixer;
use Croogo\Core\PluginManager;
use Exception;

class InstallManager
{
    const PHP_VERSION = '7.1.30';
    const CAKE_VERSION = '3.8.0';

    const DATASOURCE_REGEX = "/(\'Datasources'\s\=\>\s\[\n\s*\'default\'\s\=\>\s\[\n\X*\'__FIELD__\'\s\=\>\s\').*(\'\,)(?=\X*\'test\'\s\=\>\s)/";

    use LogTrait;

    /**
     * Default configuration
     *
     * @var array
     * @access public
     */
    public $defaultConfig = [
        'name' => 'default',
        'className' => 'Cake\Database\Connection',
        'driver' => 'Cake\Database\Driver\Mysql',
        'persistent' => false,
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'croogo',
        'port' => null,
        'schema' => null,
        'prefix' => null,
        'encoding' => 'utf8',
        'timezone' => 'UTC',
        'cacheMetadata' => true,
        'log' => false,
        'quoteIdentifiers' => false,
    ];

    /**
     *
     * @var \Croogo\Core\PluginManager
     */
    protected $_croogoPlugin;

    /**
     * Kontroler (ustawiany przez InstallController przy setupAcos)
     *
     * @var \Cake\Controller\Controller|null
     */
    public $controller;

    public function __construct()
    {
        Configure::write('Trackable.Auth.User.id', 1);
    }

    public static function versionCheck()
    {
        return [
            'php' => version_compare(phpversion(), static::PHP_VERSION, '>='),
            'cake' => version_compare(Configure::version(), static::CAKE_VERSION, '>='),
        ];
    }

    protected function _updateDatasourceConfig($path, $field, $value)
    {
        $config = file_get_contents($path);
        $config = preg_replace(
            str_replace('__FIELD__', $field, InstallManager::DATASOURCE_REGEX),
            '$1' . addslashes($value) . '$2',
            $config
        );

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        return file_put_contents($path, $config);
    }

    public function createDatabaseFile($config)
    {
        $config += $this->defaultConfig;

        if ($config['driver'] === 'Cake\Database\Driver\Postgres') {
            if (empty($config['port'])) {
                $config['port'] = 5432;
            }
        }

        ConnectionManager::drop('default');
        ConnectionManager::setConfig('default', $config);

        try {
            $db = ConnectionManager::get('default');
            $db->connect();
        } catch (MissingConnectionException $e) {
            ConnectionManager::drop('default');

            return __d('croogo', 'Could not connect to database: ') . $e->getMessage();
        }
        if (!$db->isConnected()) {
            ConnectionManager::drop('default');

            return __d('croogo', 'Could not connect to database.');
        }

        $configPath = ROOT . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
        foreach (['host', 'username', 'password', 'database', 'driver'] as $field) {
            if (isset($config[$field]) && (!empty($config[$field] || $field == 'password'))) {
                $this->_updateDatasourceConfig($configPath, $field, $config[$field]);
            }
        }

        return true;
    }

    /**
     * Mark installation as complete
     *
     * @return bool true when successful
     */
    public function installCompleted()
    {
        if (!Plugin::isLoaded('Croogo/Settings')) {
            PluginManager::load('Croogo/Settings', ['routes' => true]);
        }
        $Setting = TableRegistry::getTableLocator()->get('Croogo/Settings.Settings');
        $Setting->removeBehavior('Cached');
        if (!function_exists('mcrypt_decrypt') && !function_exists('openssl_decrypt')) {
            $Setting->write('Access Control.autoLoginDuration', '');
        }

        $Setting->updateVersionInfo();
        $Setting->updateAppVersionInfo();

        return $Setting->write('Croogo.installed', true);
    }

    /**
     * Run Migrations and add data in table
     *
     * @return bool True if migrations have succeeded
     */
    public function setupDatabase()
    {
        // Tylko pluginy obecne w tym forku (pełny Croogo miał też Blocks/Taxonomy/
        // Meta/Nodes/Comments/Contacts — usunięte z forka).
        $plugins = [
            'Croogo/Users',
            'Croogo/Acl',
            'Croogo/Settings',
            'Croogo/FileManager',
            'Croogo/Menus',
            'Croogo/Dashboards',
        ];

        $migrationsSucceed = true;
        foreach ($plugins as $plugin) {
            $migrationsSucceed = $this->runMigrations($plugin);
            if (!$migrationsSucceed) {
                $this->log('Migrations failed for ' . $plugin, LOG_CRIT);
                break;
            }
        }

        foreach ($plugins as $plugin) {
            $migrationsSucceed = $this->seedTables($plugin);
            if (!$migrationsSucceed) {
                break;
            }
        }

        if ($migrationsSucceed) {
            $fixer = new SequenceFixer();
            $fixer->fix('default');
        }

        return $migrationsSucceed;
    }

    protected function _getCroogoPlugin()
    {
        if (!($this->_croogoPlugin instanceof PluginManager)) {
            $this->_setCroogoPlugin(new PluginManager());
        }

        return $this->_croogoPlugin;
    }

    protected function _setCroogoPlugin($croogoPlugin)
    {
        unset($this->_croogoPlugin);
        $this->_croogoPlugin = $croogoPlugin;
    }

    public function runMigrations($plugin)
    {
        if (!Plugin::isLoaded($plugin)) {
            PluginManager::load($plugin);
        }
        $croogoPlugin = $this->_getCroogoPlugin();
        $result = $croogoPlugin->migrate($plugin);
        if (!$result) {
            $this->log($croogoPlugin->migrationErrors);
        }

        return $result;
    }

    public function seedTables($plugin)
    {
        if (!Plugin::isLoaded($plugin)) {
            PluginManager::load($plugin);
        }
        $croogoPlugin = $this->_getCroogoPlugin();

        return $croogoPlugin->seed($plugin);
    }

    /**
     * Create admin user
     *
     * @var User $user User entity
     * @return If user is created
     */
    public function createAdminUser($user)
    {
        $Users = \Cake\ORM\TableRegistry::getTableLocator()->get('Croogo/Users.Users');
        $Roles = \Cake\ORM\TableRegistry::getTableLocator()->get('Croogo/Users.Roles');
        $Roles->addBehavior('Croogo/Core.Aliasable');

        $user->name = $user['username'];
        $user->email = '';
        $user->timezone = 'UTC';
        $user->role_id = $Roles->getBehavior('Aliasable')->byAlias('superadmin');
        $user->status = true;
        $user->activation_key = md5(uniqid());
        if ($user->getErrors()) {
            return __d('croogo', 'Unable to create administrative user. Validation errors:');
        }

        return $Users->save($user) !== false;
    }

    public function setupAcos(): void
    {
        Cache::clearAll();
        $generator = new AclGenerator();
        if ($this->controller) {
            $dummyShell = new DummyShell();
            $generator->setShell($dummyShell);
        }
        $generator->insertAcos(ConnectionManager::get('default'));
    }

    public function setupGrants($success = null, $error = null): void
    {
        if (!$success) {
            $success = function (): void {
            };
        }
        if (!$error) {
            $error = function (): void {
            };
        }

        $Roles = \Cake\ORM\TableRegistry::getTableLocator()->get('Croogo/Users.Roles');
        $Roles->addBehavior('Croogo/Core.Aliasable');

        $Permission = \Cake\ORM\TableRegistry::getTableLocator()->get('Croogo/Acl.Permissions');
        $admin = 'Role-admin';
        $public = 'Role-public';
        $registered = 'Role-registered';
        $publisher = 'Role-publisher';

        // Tylko pluginy/akcje obecne w tym forku (pełny Croogo miał też Nodes/
        // Contacts/Blocks/Comments, a frontowy UsersController więcej akcji —
        // nieistniejące ACO -> DbAcl::allow() "Invalid node").
        $setup = [
            'controllers/Croogo\Users/Users/login' => [$public],
            'controllers/Croogo\Users/Users/logout' => [$registered],
            'controllers/Croogo\Users/Admin/Users/logout' => [$registered],

            'controllers/Croogo\Dashboards/Admin/Dashboards' => [$admin],
            'controllers/Croogo\Menus/Admin/Menus' => [$publisher],
            'controllers/Croogo\Menus/Admin/Links' => [$publisher],
            'controllers/Croogo\FileManager/Admin/Attachments' => [$publisher],
            'controllers/Croogo\FileManager/Admin/FileManager' => [$publisher],
            'controllers/Croogo\Users/Admin/Users/view' => [$admin],
        ];

        foreach ($setup as $aco => $roles) {
            foreach ($roles as $aro) {
                try {
                    $result = $Permission->allow($aro, $aco);
                    if ($result) {
                        $success(__d('croogo', 'Permission %s granted to %s', $aco, $aro));
                    }
                } catch (Exception $e) {
                    $error($e->getMessage());
                }
            }
        }
    }
}

//phpcs:disable
// Cake 5: Shell usunięty -> adapter na ConsoleIo (AclExtras::setShell przyjmuje ConsoleIo).
class DummyShell extends ConsoleIo
{
    use LogTrait;
    public function out(array|string $message = '', int $newlines = 1, int $level = ConsoleIo::NORMAL): ?int
    {
        $msg = preg_replace('/\<\/?\w+\>/', '', is_array($message) ? implode("\n", $message) : (string)$message);
        $this->log($msg);

        return null;
    }
}
