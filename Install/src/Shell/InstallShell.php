<?php

namespace Croogo\Install\Shell;

use App\Console\Installer;
use App\Controller\Component\AuthComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Croogo\Acl\AclGenerator;
use Croogo\Core\Plugin;
use Croogo\Install\InstallManager;
use Croogo\Install\Model\Table\InstallTable;
use Composer\IO\BufferIO;

/**
 * Install Shell
 *
 * @category Shell
 * @package  Croogo.Install.Console.Command
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class InstallShell extends Shell
{

    public function startup()
    {
        $options = ['bootstrap' => true, 'routes' => true];
        $plugins = array_merge(Plugin::$corePlugins, Plugin::$bundledPlugins);
        foreach ($plugins as $plugin) {
            if (!Plugin::loaded($plugin)) {
                Plugin::load($plugin, $options);
            }
        }
    }

/**
 * Display help/options
 */
    public function getOptionParser()
    {
        $drivers = ['Mysql', 'Postgres', 'Sqlite', 'Sqlserver'];
        $parser = parent::getOptionParser();
        $parser->description(__d('croogo', 'Install Utilities'))
            ->addSubcommand('main', [
                'help' => 'Generate database.php and create admin user.',
                'parser' => [
                    'description' => 'Generate database.php and create admin user.',
                ]
            ])
            ->addSubcommand('setup_grants', [
                'help' => 'Setup default grants (ACOs) for roles',
                'parser' => [
                    'description' => 'Generate default role settings during release',
                ]
            ])
            ->addOption('datasource', [
                'help' => 'Database Driver',
                'short' => 'd',
                'required' => true,
                'options' => $drivers,
            ])
            ->addOption('host', [
                'help' => 'Database Host',
                'short' => 'h',
                'required' => true,
            ])
            ->addOption('username', [
                'help' => 'Database User',
                'short' => 'u',
                'required' => true,
            ])
            ->addOption('password', [
                'help' => 'Database Password',
                'short' => 'p',
                'required' => true,
            ])
            ->addOption('database-name', [
                'help' => 'Database Name',
                'short' => 'n',
                'required' => true,
            ])
            ->addOption('port', [
                'help' => 'Database Port',
                'short' => 't',
                'required' => true,
            ])
            //->addOption('prefix', [
                //'help' => 'Table Prefix',
                //'short' => 'x',
                //'required' => true,
            //])
            ->addArgument('admin-user', [
                'help' => 'Admin username',
            ])
            ->addArgument('admin-password', [
                'help' => 'Admin password',
            ]);
        return $parser;
    }

/**
 * Convenient wrapper for Shell::in() that gets the default from CLI options
 */
    protected function _in($prompt, $options = null, $default = null, $argument = null)
    {
        if (isset($this->params[$argument])) {
            return $this->params[$argument];
        }
        return $this->in($prompt, $options, $default);
    }

/**
 * Convenient wrapper for Shell::in() that gets the default from CLI argument
 */
    protected function _args($prompt, $options = null, $default = null, $index = null)
    {
        if (!empty($this->args[$index])) {
            return $this->args[$index];
        }
        return $this->in($prompt, $options, $default);
    }

    public function main()
    {
        Installer::setSecuritySalt(ROOT, new BufferIO());
        $this->out();
        $this->out('Database settings:');
        $install['datasource'] = $this->_in(__d('croogo', 'DataSource'), [
            'Mysql',
            'Sqlite',
            'Postgres',
            'Sqlserver'
        ], 'Mysql', 'datasource');
        $install['driver'] = 'Cake\Database\Driver\\' . $install['datasource'];
        $install['host'] = $this->_in(__d('croogo', 'Host'), null, 'localhost', 'host');
        $install['username'] = $this->_in(__d('croogo', 'Login'), null, 'root', 'username');
        $install['password'] = $this->_in(__d('croogo', 'Password'), null, '', 'password');
        $install['database'] = $this->_in(__d('croogo', 'Database'), null, 'croogo', 'database-name');
        //$install['prefix'] = $this->_in(__d('croogo', 'Prefix'), null, '', 'prefix');
        $install['port'] = $this->_in(__d('croogo', 'Port'), null, null, 'port');

        $InstallManager = new InstallManager();
        $isFileCreated = $InstallManager->createDatabaseFile($install);
        if ($isFileCreated !== true) {
            $this->err($isFileCreated);
            return $this->_stop();
        }

        $this->out('Setting up database objects. Please wait...');
        try {
            $result = $InstallManager->setupDatabase();
            if ($result !== true) {
                $this->err($result);
                return $this->_stop();
            }
        } catch (\Exception $e) {
            $this->err($e->getMessage());
            $this->err('Please verify you have the correct credentials');
            return $this->_stop();
        }

        try {
            $this->out('Setting up access control objects. Please wait...');
            $generator = new AclGenerator();
            $generator->Shell = $this;
            $generator->insertAcos(ConnectionManager::get('default'));
            $InstallManager->setupGrants();
        } catch (\Exception $e) {
            $this->err('Error installing access control objects');
            $this->err($e->getMessage());
            return $this->_stop();
        }

        if (empty($this->args)) {
            $this->out();
            $this->out('Create Admin user:');
        }

        do {
            $username = $this->_args(__d('croogo', 'Username'), null, null, 0);
            if (empty($username)) {
                $this->err('Username must not be empty');
            }
        } while (empty($username));

        do {
            $password = $this->_args(__d('croogo', 'Password'), null, null, 1);
            if (empty($this->args)) {
                $verify = $this->_in(__d('croogo', 'Verify Password'), null, null, 1);
                $passwordsMatched = $password == $verify;

                if (!$passwordsMatched) {
                    $this->err('Passwords do not match');
                }
            } else {
                $passwordsMatched = true;
            }
            if (empty($password)) {
                $this->err('Password must not be empty');
            }
        } while (empty($password) || !$passwordsMatched);

        $user = ['username' => $username, 'password' => $password];

        try {
            $this->out('Setting up admin user. Please wait...');
            $Install = TableRegistry::get('Croogo/Install.Install');
            $Install->addAdminUser($user);
            $InstallManager->installCompleted();
        } catch (\Exception $e) {
            $this->err('Error creating admin user: ' . $e->getMessage());
        }

        $this->out();
        $this->success('Congratulations, Croogo has been installed successfully.');
    }
}
