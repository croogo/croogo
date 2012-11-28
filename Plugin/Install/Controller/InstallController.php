<?php

App::uses('File', 'Utility');

/**
 * Install Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class InstallController extends Controller {

/**
 * No models required
 *
 * @var array
 * @access public
 */
	public $uses = 'Install.Install';

/**
 * No components required
 *
 * @var array
 * @access public
 */
	public $components = array('Session');

/**
 * Helpers
 *
 * @var array
 * @access public
 */
	public $helpers = array(
		'Html' => array(
			'className' => 'CroogoHtml',
		),
		'Form' => array(
			'className' => 'CroogoForm',
		),
		'Layout',
	);

/**
 * Default configuration
 *
 * @var array
 * @access public
 */
	public $defaultConfig = array(
		'name' => 'default',
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'root',
		'password' => '',
		'database' => 'croogo',
		'schema' => null,
		'prefix' => null,
		'encoding' => 'UTF8',
		'port' => null,
	);

/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->layout = 'install';
		$this->_generateAssets();
	}

/**
 * Generate assets
 */
	protected function _generateAssets() {
		if (!file_exists(CSS . 'croogo-bootstrap.css')) {
			App::uses('AssetGenerator', 'Install.Lib');
			$generator = new AssetGenerator();
			try {
				$generator->generate();
			} catch (Exception $e) {
				$this->log($e->getMessage());
				$this->Session->setFlash('Asset generation failed. Please verify that dependencies exists and readable.', 'default', array('class' => 'error'));
			}
		}
	}

/**
 * If settings.json exists, app is already installed
 *
 * @return void
 */
	protected function _check() {
		if (Configure::read('Install.installed') && Configure::read('Install.secured')) {
			$this->Session->setFlash('Already Installed');
			$this->redirect('/');
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
	public function index() {
		$this->_check();
		$this->set('title_for_layout', __('Installation: Welcome'));
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
	public function database() {
		$this->_check();
		$this->set('title_for_layout', __('Step 1: Database'));

		if (Configure::read('Install.installed')) {
			$this->redirect(array('action' => 'adminuser'));
		}
		if (file_exists(APP . 'Config' . DS . 'database.php')) {
			if (!copy(APP . 'Config' . DS . 'croogo.php.install', APP . 'Config' . DS . 'croogo.php')) {
				$this->Session->setFlash(__('Could not write croogo.php file.'), 'default', array('class' => 'error'));
			} else {
				$this->Session->setFlash('Skipping database setup');
				$this->redirect(array('action' => 'data'));
			}
		}

		if (empty($this->request->data)) {
			return;
		}

		App::uses('ConnectionManager', 'Model');
		$config = $this->defaultConfig;
		foreach ($this->request->data['Install'] as $key => $value) {
			if (isset($this->request->data['Install'][$key])) {
				$config[$key] = $value;
			}
		}
		try {
			ConnectionManager::create('default', $config);
			$db = ConnectionManager::getDataSource('default');
		}
		catch (MissingConnectionException $e) {
			$this->Session->setFlash(__('Could not connect to database: %s', $e->getMessage()), 'default', array('class' => 'error'));
			return;
		}
		if (!$db->isConnected()) {
			$this->Session->setFlash(__('Could not connect to database.'), 'default', array('class' => 'error'));
			return;
		}

		copy(APP . 'Config' . DS . 'database.php.install', APP . 'Config' . DS . 'database.php');
		$file = new File(APP . 'Config' . DS . 'database.php', true);
		$content = $file->read();

		foreach ($config as $configKey => $configValue) {
			$content = str_replace('{default_' . $configKey . '}', $configValue, $content);
		}

		if (!$file->write($content)) {
			$this->Session->setFlash(__('Could not write database.php file.'), 'default', array('class' => 'error'));
			return;
		}

		if (copy(APP . 'Config' . DS . 'croogo.php.install', APP . 'Config' . DS . 'croogo.php')) {
			$this->redirect(array('action' => 'data'));
		} else {
			$this->Session->setFlash(__('Could not write croogo.php file.'), 'default', array('class' => 'error'));
		}
	}

/**
 * Step 2: Run the initial sql scripts to create the db and seed it with data
 *
 * @return void
 * @access public
 */
	public function data() {
		$this->_check();
		$this->set('title_for_layout', __('Step 2: Build database'));
		if (isset($this->params['named']['run'])) {
			App::uses('File', 'Utility');
			App::uses('CakeSchema', 'Model');
			App::uses('ConnectionManager', 'Model');

			$plugins = Configure::read('Core.corePlugins');
			foreach ($plugins as $plugin) {
				$this->Install->runMigrations($plugin);
			}

			$path = App::pluginPath('Install') . DS . 'Config' . DS . 'Data' . DS;
			$dataObjects = App::objects('class', $path);
			foreach ($dataObjects as $data) {
				include ($path . $data . '.php');
				$classVars = get_class_vars($data);
				$modelAlias = substr($data, 0, -4);
				$table = $classVars['table'];
				$records = $classVars['records'];
				App::uses('Model', 'Model');
				$modelObject =& new Model(array(
					'name' => $modelAlias,
					'table' => $table,
					'ds' => 'default',
				));
				if (is_array($records) && count($records) > 0) {
					foreach ($records as $record) {
						$modelObject->create($record);
						$modelObject->save();
					}
					$modelObject->getDatasource()->resetSequence(
						$modelObject->useTable, $modelObject->primaryKey
					);
				}
			}

			$this->redirect(array('action' => 'adminuser'));
		}
	}

/**
 * Step 3: get username and passwords for administrative user
 */
	public function adminuser() {
		if ($this->request->is('post')) {
			if (!CakePlugin::loaded('Users')) {
				CakePlugin::load('Users');
			}
			$this->loadModel('Users.User');
			$this->User->set($this->request->data);
			if ($this->User->validates()) {
				$token = uniqid();
				$this->Session->write('Install', array(
					'token' => $token,
					'User' => $this->request->data['User'],
					));
				$this->redirect(array('action' => 'finish', $token));
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
	public function finish($token = null) {
		$this->set('title_for_layout', __('Installation successful'));
		$this->_check();
		$this->loadModel('Install.Install');
		$install = $this->Session->read('Install');
		if ($install['token'] == $token) {
			unset($install['token']);
			if ($this->Install->finalize($install)) {
				$urlBlogAdd = Router::url(array(
					'plugin' => 'nodes',
					'admin' => true,
					'controller' => 'nodes',
					'action' => 'add',
					'blog',
				));
				$urlSettings = Router::url(array(
					'plugin' => 'settings',
					'admin' => true,
					'controller' => 'settings',
					'action' => 'prefix',
					'Site',
				));
				$this->set('user', $install);
				$this->set(compact('urlBlogAdd', 'urlSettings'));
			} else {
				$this->Session->setFlash('Something went wrong during installation. Please check your server logs.');
				$this->redirect(array('action' => 'adminuser'));
			}
			$this->Session->delete('Install');
		} else {
			$this->redirect('/');
		}
	}

}
