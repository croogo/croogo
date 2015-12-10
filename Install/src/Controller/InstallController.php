<?php

use Cake\Core\Configure;
use Cake\Core\Plugin;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Utility\File;
use Install\AssetGenerator;
use Install\InstallManager;

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

/**
 * Components
 *
 * @var array
 * @access public
 */
    public $components = ['Session'];

/**
 * Helpers
 *
 * @var array
 * @access public
 */
    public $helpers = [
        'Html' => [
            'className' => 'CroogoHtml',
        ],
        'Form' => [
            'className' => 'CroogoForm',
        ],
        'Croogo.Layout',
    ];

/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->layout = 'install';

        $croogoTheme = new CroogoTheme();
        $data = $croogoTheme->getData($this->theme);
        $settings = $data['settings'];
        $this->set('themeSettings', $settings);
        $this->_generateAssets();
    }

/**
 * Generate assets
 */
    protected function _generateAssets()
    {
        $file = Plugin::path('Croogo') . 'webroot' . DS . 'css' . DS . 'croogo-bootstrap.css';
        if (!file_exists($file)) {
                        $generator = new AssetGenerator();
            try {
                $generator->generate();
            } catch (Exception $e) {
                $this->log($e->getMessage());
                $this->Session->setFlash('Asset generation failed. Please verify that dependencies exists and readable.', 'flash', ['class' => 'error']);
            }
        }
    }

/**
 * If settings.json exists, app is already installed
 *
 * @return void
 */
    protected function _check()
    {
        if (Configure::read('Croogo.installed') && Configure::read('Install.secured')) {
            $this->Session->setFlash('Already Installed');
            return $this->redirect('/');
        }
    }

/**
 * Step 0: welcome
 *
 * A simple welcome message for the installer.
 *
 * @return void
 * @access public
 */
    public function index()
    {
        $this->_check();
        $this->set('title_for_layout', __d('croogo', 'Installation: Welcome'));
    }

/**
 * Step 1: database
 *
 * Try to connect to the database and give a message if that's not possible so the user can check their
 * credentials or create the missing database
 * Create the database file and insert the submitted details
 *
 * @return void
 * @access public
 */
    public function database()
    {
        $this->_check();
        $this->set('title_for_layout', __d('croogo', 'Step 1: Database'));

        if (Configure::read('Croogo.installed')) {
            return $this->redirect(['action' => 'adminuser']);
        }

        if (!empty($this->request->data)) {
            $InstallManager = new InstallManager();
            $result = $InstallManager->createDatabaseFile([
                'Install' => $this->request->data,
            ]);
            if ($result !== true) {
                $this->Session->setFlash($result, 'flash', ['class' => 'error']);
            } else {
                return $this->redirect(['action' => 'data']);
            }
        }

        $currentConfiguration = [
            'exists' => false,
            'valid' => false,
        ];
        if (file_exists(APP . 'config' . DS . 'database.php')) {
            $currentConfiguration['exists'] = true;
        }
        if ($currentConfiguration['exists']) {
            try {
                $this->loadModel('Install.Install');
                $ds = $this->Install->getDataSource();
                $ds->cacheSources = false;
                $sources = $ds->listSources();
                $currentConfiguration['valid'] = true;
            } catch (Exception $e) {
            }
        }
        $this->set(compact('currentConfiguration'));
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
        $this->set('title_for_layout', __d('croogo', 'Step 2: Build database'));

        $this->loadModel('Install.Install');
        $ds = $this->Install->getDataSource();
        $ds->cacheSources = false;
        $sources = $ds->listSources();
        if (!empty($sources)) {
            $this->Session->setFlash(
                __d('croogo', 'Warning: Database "%s" is not empty.', $ds->config['database']),
                'default',
                ['class' => 'error']
            );
        }

        if ($this->request->query('run')) {
            set_time_limit(10 * MINUTE);
            $this->Install->setupDatabase();

            $InstallManager = new InstallManager();
            $result = $InstallManager->createCroogoFile();
            if ($result !== true) {
                return $this->Session->setFlash($result, 'flash', ['class' => 'error']);
            }

            return $this->redirect(['action' => 'adminuser']);
        }
    }

/**
 * Step 3: get username and passwords for administrative user
 */
    public function adminuser()
    {
        if (!file_exists(APP . 'config' . DS . 'database.php')) {
            return $this->redirect('/');
        }

        if ($this->request->is('post')) {
            if (!Plugin::loaded('Users')) {
                Plugin::load('Users');
            }
            $this->loadModel('Users.User');
            $this->User->set($this->request->data);
            if ($this->User->validates()) {
                $user = $this->Install->addAdminUser($this->request->data);
                if ($user) {
                    $this->Session->write('Install.user', $user);
                    return $this->redirect(['action' => 'finish']);
                }
            }
        }
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
        $this->set('title_for_layout', __d('croogo', 'Installation successful'));
        $this->_check();

        $InstallManager = new InstallManager();
        $installed = $InstallManager->createSettingsFile();
        if ($installed === true) {
            $InstallManager->installCompleted();
        } else {
            $this->set('title_for_layout', __d('croogo', 'Installation failed'));
            $msg = __d('croogo', 'Installation failed: Unable to create settings file');
            $this->Session->setFlash($msg, 'flash', ['class' => 'error']);
        }

        $urlBlogAdd = Router::url([
            'plugin' => 'nodes',
            'admin' => true,
            'controller' => 'nodes',
            'action' => 'add',
            'blog',
        ]);
        $urlSettings = Router::url([
            'plugin' => 'settings',
            'admin' => true,
            'controller' => 'settings',
            'action' => 'prefix',
            'Site',
        ]);

        $this->set('user', $this->Session->read('Install.user'));
        if ($installed) {
            $this->Session->destroy();
        }
        $this->set(compact('urlBlogAdd', 'urlSettings', 'installed'));
    }
}
