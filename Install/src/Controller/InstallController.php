<?php

namespace Croogo\Install\Controller;

use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Driver\Mysql;
use Cake\Database\Driver\Sqlite;
use Cake\Database\Driver\Postgres;
use Cake\Database\Driver\Sqlserver;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\Exception\PersistenceFailedException;
use Croogo\Install\InstallManager;
use Exception;

/**
 * Install Controller
 *
 * @category Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class InstallController extends Controller
{

    const STEPS = [
        'Welcome', 'Database', 'Admin user', 'Completed'
    ];

    /** @var \Croogo\Install\InstallManager */
    protected $installManager;

    public function initialize()
    {
        $this->loadComponent('Flash');

        parent::initialize();
        $this->installManager = new InstallManager();
    }

    /**
     * beforeFilter
     *
     * @return void
     * @access public
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setTheme('Croogo/Core');
        $this->viewBuilder()->setLayout('install');
        $this->viewBuilder()->setClassName('Croogo/Core.Croogo');
        $this->viewBuilder()->setHelpers([
            'Croogo/Core.Theme',
            'Html' => [
                'className' => 'Croogo/Core.Html',
            ],
            'Form' => [
                'className' => 'Croogo/Core.Form',
            ],
        ]);
    }

    /**
     * If settings.json exists, app is already installed
     *
     * @return \Cake\Http\Response|void
     */
    protected function _check()
    {
        $this->installManager->replaceSalt();
        if (extension_loaded('pdo_mysql')):
            $drivers[Mysql::class] = 'MySQL';
        endif;
        if (extension_loaded('pdo_sqlite')):
            $drivers[Sqlite::class] = 'SQLite';
        endif;
        if (extension_loaded('pdo_pgsql')):
            $drivers[Postgres::class] = 'PostgreSQL';
        endif;
        if (extension_loaded('pdo_sqlsrv')):
            $drivers[Sqlserver::class] = 'Microsoft SQL Server';
        endif;
        $this->set(compact('drivers'));

        if (Configure::read('Croogo.installed') && Configure::read('Install.secured')) {
            $this->Flash->error('Already Installed');

            return $this->redirect('/');
        }
    }

    /**
     * Step 1: welcome
     *
     * A simple welcome message for the installer.
     *
     * @return void
     * @access public
     */
    public function index()
    {
        $this->_check();

        $this->set('onStep', 1);
    }

    /**
     * Step 1: database
     *
     * Try to connect to the database and give a message if that's not possible so the user can check their
     * credentials or create the missing database
     * Create the database file and insert the submitted details
     *
     * @return \Cake\Http\Response|void
     * @access public
     */
    public function database()
    {
        $this->_check();

        if (Configure::read('Croogo.installed')) {
            return $this->redirect(['action' => 'adminuser']);
        }

        if ($this->getRequest()->is('post')) {
            $result = $this->installManager->createDatabaseFile($this->getRequest()->getData());
            if ($result !== true) {
                $this->Flash->error($result);
            } else {
                return $this->redirect(['action' => 'data']);
            }
        }

        $currentConfiguration = [
            'exists' => false,
            'valid' => false,
        ];
        $context = [
            'schema' => true,
            'defaults' => [
                'driver' => '',
                'host' => 'localhost',
                'username' => 'root',
                'database' => 'croogo',
            ],
        ];
        try {
            /** @var \Cake\Database\Connection */
            $connection = ConnectionManager::get('default');
            $config = $connection->config();
            $currentConfiguration['exists'] = !empty($config) && !($config['username'] === 'my_app' && $config['database'] === 'my_app');
            $currentConfiguration['valid'] = $connection->connect();
            $context = [
                'schema' => true,
                'defaults' => $config,
            ];
        } catch (Exception $e) {
        }
        $this->set(compact('context', 'currentConfiguration'));
        $this->set('onStep', 2);
    }

    /**
     * Step 2: Run the initial sql scripts to create the db and seed it with data
     *
     * @return void
     * @access public
     */
    public function data()
    {
        $this->_check();

        /** @var \Cake\Database\Connection */
        $connection = ConnectionManager::get('default');
        $connection->cacheMetadata(false);
        $schemaCollection = $connection->getSchemaCollection();
        if (!empty($schemaCollection->listTables())) {
            $this->Flash->error(
                __d('croogo', 'Warning: Database "%s" is not empty.', $connection->config()['database'])
            );

            $this->set('onStep', 2);

            return;
        }

        set_time_limit(10 * MINUTE);
        $result = $this->installManager->setupDatabase();

        if ($result !== true) {
            $this->Flash->error($result === false ? __d('croogo', 'There was a problem installing Croogo') : $result);

            return $this->redirect(['action' => 'undo']);
        }

        return $this->redirect(['action' => 'acl']);
    }

    public function acl()
    {
        try {
            $this->installManager->controller = $this;
            $this->installManager->setupAcos();
            $this->installManager->setupGrants();

            return $this->redirect(['action' => 'adminUser']);
        } catch (Exception $e) {
            $this->Flash->error(__d('croogo', 'Error installing access control objects'));
            $this->Flash->error($e->getMessage());

            return $this->redirect(['action' => 'undo']);
        }
    }

    /**
     * Undoes all previous database work
     * @return void
     */
    public function undo()
    {
    }

    /**
     * Step 3: get username and passwords for administrative user
     */
    public function adminUser()
    {
        $this->_check();
        if (!Plugin::isLoaded('Croogo/Users')) {
            Plugin::load('Croogo/Users');
        }
        $this->loadModel('Croogo/Users.Users');

        $user = $this->Users->get(1);

        if ($this->getRequest()->is('put')) {
            Configure::write('Trackable.Auth.User.id', 1);
            try {
                $result = $this->Install->addAdminUser($this->getRequest()->getData());
                $this->getRequest()->getSession()->write('Install.user', $result);

                return $this->redirect(['action' => 'finish']);
            } catch (PersistenceFailedException $e) {
                $this->Flash->error($e->getMessage());
            }
        }

        $this->set('user', $user);
        $this->set('onStep', 3);
    }

    /**
     * Step 4: finish
     *
     * Copy settings.json file into place and create user obtained in step 3
     *
     * @return void
     * @access public
     */
    public function finish($token = null)
    {
        $this->_check();

        $this->installManager->installCompleted();

        $this->set('user', $this->getRequest()->getSession()->read('Install.user'));
        $this->getRequest()->getSession()->destroy();
        $this->set('onStep', 4);
        Cache::clearAll();
    }
}
