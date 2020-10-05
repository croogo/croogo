<?php

namespace Croogo\Install;

use Cake\Cache\Cache;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Exception\MissingConnectionException;
use Cake\Datasource\ConnectionManager;
use Cake\Log\LogTrait;
use Cake\Utility\Security;
use Cake\ORM\TableRegistry;
use Croogo\Acl\AclGenerator;
use Croogo\Core\Database\SequenceFixer;
use Croogo\Core\PluginManager;
use Exception;

class InstallManager
{
    const PHP_VERSION = '7.1.30';
    const CAKE_VERSION = '3.8.0';

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

    /**
     * Set the security.salt value in application config file
     *
     * @return string Success or error message
     */
    public function replaceSalt()
    {
        $file = ROOT . '/config/app.php';
        $content = file_get_contents($file);
        $newKey = hash('sha256', Security::randomBytes(64));

        $content = str_replace('__SALT__', $newKey, $content, $count);

        if ($count == 0) {
            return 'No Security.salt placeholder to replace.';
        }

        $result = file_put_contents($file, $content);
        if ($result) {
            return 'Updated Security.salt value in config/' . $file;
        }

        return 'Unable to update Security.salt value.';
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
            /** @var \Cake\Database\Connection */
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
        DatasourceConfigUpdater::update($configPath, $config);

        return true;
    }

    /**
     * Mark installation as complete
     *
     * @return bool true when successful
     */
    public function installCompleted()
    {
        PluginManager::load('Croogo/Settings', ['routes' => true]);
        $Setting = TableRegistry::get('Croogo/Settings.Settings');
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
        $plugins = [
            'Croogo/Users',
            'Croogo/Acl',
            'Croogo/Settings',
            'Croogo/Blocks',
            'Croogo/Taxonomy',
            'Croogo/FileManager',
            'Croogo/Meta',
            'Croogo/Nodes',
            'Croogo/Comments',
            'Croogo/Contacts',
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

    public function setupAcos()
    {
        Cache::clearAll();
        $generator = new AclGenerator();
        if ($this->controller) {
            $dummyShell = new DummyShell();
            $generator->setShell($dummyShell);
        }
        $generator->insertAcos(ConnectionManager::get('default'));
    }

    public function setupGrants($success = null, $error = null)
    {
        if (!$success) {
            $success = function () {
            };
        }
        if (!$error) {
            $error = function () {
            };
        }

        $Roles = TableRegistry::get('Croogo/Users.Roles');
        $Roles->addBehavior('Croogo/Core.Aliasable');

        $Permission = TableRegistry::get('Croogo/Acl.Permissions');
        $admin = 'Role-admin';
        $public = 'Role-public';
        $registered = 'Role-registered';
        $publisher = 'Role-publisher';

        $setup = [
            //            'controllers/Croogo\Comments/Comments/index' => [$public],
            //            'controllers/Croogo\Comments/Comments/add' => [$public],
            //            'controllers/Croogo\Comments/Comments/delete' => [$registered],
            'controllers/Croogo\Contacts/Contacts/view' => [$public],
            'controllers/Croogo\Nodes/Nodes/index' => [$public],
            'controllers/Croogo\Nodes/Nodes/feed' => [$public],
            'controllers/Croogo\Nodes/Nodes/term' => [$public],
            'controllers/Croogo\Nodes/Nodes/promoted' => [$public],
            'controllers/Croogo\Nodes/Nodes/search' => [$public],
            'controllers/Croogo\Nodes/Nodes/view' => [$public],
            'controllers/Croogo\Users/Users/index' => [$registered],
            'controllers/Croogo\Users/Users/add' => [$public],
            'controllers/Croogo\Users/Users/activate' => [$public],
            'controllers/Croogo\Users/Users/edit' => [$registered],
            'controllers/Croogo\Users/Users/forgot' => [$public],
            'controllers/Croogo\Users/Users/reset' => [$public],
            'controllers/Croogo\Users/Users/login' => [$public],
            'controllers/Croogo\Users/Users/logout' => [$public, $registered],
            'controllers/Croogo\Users/Admin/Users/logout' => [$public, $registered],
            'controllers/Croogo\Users/Users/view' => [$registered],

            'controllers/Croogo\Dashboards/Admin/Dashboards' => [$admin],
            'controllers/Croogo\Nodes/Admin/Nodes' => [$publisher],
            'controllers/Croogo\Menus/Admin/Menus' => [$publisher],
            'controllers/Croogo\Menus/Admin/Links' => [$publisher],
            'controllers/Croogo\Blocks/Admin/Blocks' => [$publisher],
            'controllers/Croogo\FileManager/Admin/Attachments' => [$publisher],
            'controllers/Croogo\FileManager/Admin/FileManager' => [$publisher],
            'controllers/Croogo\Contacts/Admin/Contacts' => [$publisher],
            'controllers/Croogo\Contacts/Admin/Messages' => [$publisher],
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
class DummyShell extends Shell
{
    use LogTrait;
    public function out($msg = null, $newlines = 1, $level = Shell::NORMAL)
    {
        $msg = preg_replace('/\<\/?\w+\>/', null, $msg);
        $this->log($msg);
    }
}
