<?php

namespace Croogo\Install;

use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

class InstallManager
{

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

    public function createDatabaseFile($data)
    {
        $config = $this->defaultConfig;

        foreach ($data['Install'] as $key => $value) {
            if (isset($data['Install'][$key])) {
                $config[$key] = $value;
            }
        }

        Configure::write('Datasources', ['default' => $config ]);

        Configure::config('dbConfig', new PhpConfig(ROOT . DS . 'config' . DS));
        if (!Configure::dump('database', 'dbConfig', ['Datasources'])) {
            return __d('croogo', 'Could not write database.php file.');
        }

        Configure::load('database', 'default');
        ConnectionManager::drop('default');
        ConnectionManager::config(Configure::consume('Datasources'));

        try {
            $db = ConnectionManager::get('default');
        } catch (MissingConnectionException $e) {
            return __d('croogo', 'Could not connect to database: ') . $e->getMessage();
        }
        if (!$db->isConnected()) {
            return __d('croogo', 'Could not connect to database.');
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
        $Setting = TableRegistry::get('Croogo/Settings.Settings');
        $Setting->removeBehavior('Cached');
        if (!function_exists('mcrypt_decrypt')) {
            $Setting->write('Access Control.autoLoginDuration', '');
        }
        return $Setting->write('Croogo.installed', 1);
    }
}
