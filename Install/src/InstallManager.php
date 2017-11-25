<?php

namespace Croogo\Install;

use Cake\Core\Configure;
use Cake\Database\Exception\MissingConnectionException;
use Cake\Datasource\ConnectionManager;
use Cake\Log\LogTrait;
use Cake\ORM\TableRegistry;
use Croogo\Acl\AclGenerator;
use Croogo\Core\Plugin;
use Croogo\Core\Database\SequenceFixer;

class InstallManager
{
    const PHP_VERSION = '5.5.9';
    const CAKE_VERSION = '3.4.8';

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
     * @var \Croogo\Core\Plugin
     */
    protected $_croogoPlugin;

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
        ConnectionManager::config('default', $config);

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
        Plugin::load('Croogo/Settings', ['routes' => true]);
        $Setting = TableRegistry::get('Croogo/Settings.Settings');
        $Setting->removeBehavior('Cached');
        if (!function_exists('mcrypt_decrypt')) {
            $Setting->write('Access Control.autoLoginDuration', '');
        }

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
            'Croogo/Blocks',
            'Croogo/Taxonomy',
            'Croogo/FileManager',
            'Croogo/Meta',
            'Croogo/Nodes',
            'Croogo/Comments',
            'Croogo/Contacts',
            'Croogo/Menus',
            'Croogo/Dashboards',
            'Croogo/Settings',
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
        if (!($this->_croogoPlugin instanceof Plugin)) {
            $this->_setCroogoPlugin(new Plugin());
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
        if (!Plugin::loaded($plugin)) {
            Plugin::load($plugin);
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
        if (!Plugin::loaded($plugin)) {
            Plugin::load($plugin);
        }
        $croogoPlugin = $this->_getCroogoPlugin();

        return $croogoPlugin->seed($plugin);
    }

    /**
     * Create admin user
     *
     * @var array $user User datas
     * @return If user is created
     */
    public function createAdminUser($user)
    {
        $Users = TableRegistry::get('Croogo/Users.Users');
        $Roles = TableRegistry::get('Croogo/Users.Roles');
        $Roles->addBehavior('Croogo/Core.Aliasable');

        if (is_array($user)) {
            $user = $Users->newEntity($user);
        }

        $user->name = $user['username'];
        $user->email = '';
        $user->timezone = 'UTC';
        $user->role_id = $Roles->byAlias('superadmin');
        $user->status = true;
        $user->activation_key = md5(uniqid());
        if ($user->errors()) {
            return __d('croogo', 'Unable to create administrative user. Validation errors:');
        }

        return $Users->save($user) !== false;
    }

    public function setupAcos()
    {
        $generator = new AclGenerator();
        if ($this->controller) {
            $dummyShell = new DummyShell();
            $generator->Shell = $dummyShell;
        }
        $generator->insertAcos(ConnectionManager::get('default'));
    }

    public function setupGrants($success = null, $error = null)
    {
        if (!$success) {
            $success = function() {
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
            'controllers/Croogo\Users/Users/logout' => [$registered],
            'controllers/Croogo\Users/Admin/Users/logout' => [$registered],
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
                } catch (\Exception $e) {
                    $error($e->getMessage());
                }
            }
        }
    }
}

class DummyShell {
    use LogTrait;
    function out($msg, $newlines = 1, $level = 1) {
        $msg = preg_replace('/\<\/?\w+\>/', null, $msg);
        $this->log($msg);
    }
}
