<?php

namespace Croogo\Install\Controller;

use App\Console\Installer;
use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Utility\File;
use Composer\IO\BufferIO;
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

    public function initialize()
    {
        $this->loadComponent('Flash');

        parent::initialize();
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

        Installer::setSecuritySalt(ROOT, new BufferIO());

        if ($this->getRequest()->is('post')) {
            $InstallManager = new InstallManager();
            $result = $InstallManager->createDatabaseFile($this->getRequest()->getData());
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
                'driver' => 'Cake\Database\Driver\Mysql',
                'host' => 'localhost',
                'username' => 'root',
                'database' => 'croogo',
            ],
        ];
        try {
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

        $install = new InstallManager();
        set_time_limit(10 * MINUTE);
        $result = $install->setupDatabase();

        if ($result !== true) {
            $this->Flash->error($result === false ? __d('croogo', 'There was a problem installing Croogo') : $result);

            return $this->redirect(['action' => 'undo']);
        }

        return $this->redirect(['action' => 'acl']);
    }

    public function acl()
    {
        try {
            $install = new InstallManager();
            $install->controller = $this;
            $install->setupAcos();
            $install->setupGrants();

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
            $user = $this->Users->patchEntity($user, $this->getRequest()->getData());
            $install = new InstallManager();
            $result = $install->createAdminUser($user);
            if ($result === true) {
                $this->getRequest()->getSession()->write('Install.user', $user);

                return $this->redirect(['action' => 'finish']);
            }
            $this->Flash->error($result);
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

        $install = new InstallManager();
        $install->installCompleted();

        $this->set('user', $this->getRequest()->getSession()->read('Install.user'));
        $this->getRequest()->getSession()->destroy();
        $this->set('onStep', 4);
        Cache::clearAll();
    }
}
