<?php
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
class InstallController extends InstallAppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'Install';

/**
 * No models required
 *
 * @var array
 * @access public
 */
	public $uses = null;

/**
 * No components required
 *
 * @var array
 * @access public
 */
	public $components = null;

/**
 * Default configuration
 *
 * @var array
 * @access public
 */
	public $defaultConfig = array(
		'name' => 'default',
		'driver'=> 'mysql',
		'persistent'=> false,
		'host'=> 'localhost',
		'login'=> 'root',
		'password'=> '',
		'database'=> 'croogo',
		'schema'=> null,
		'prefix'=> null,
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
		App::import('Component', 'Session');
		$this->Session = new SessionComponent;
	}

/**
 * If settings.yml exists, app is already installed
 *
 * @return void
 */
	protected function _check() {
		if (file_exists(CONFIGS . 'settings.yml')) {
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
		$this->set('title_for_layout', __('Installation: Welcome', true));
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
		$this->set('title_for_layout', __('Step 1: Database', true));

		if (empty($this->data)) {
			return;
		}

		@App::import('Model', 'ConnectionManager');
		$config = $this->defaultConfig;
		foreach ($this->data['Install'] AS $key => $value) {
			if (isset($this->data['Install'][$key])) {
				$config[$key] = $value;
			}
		}
		@ConnectionManager::create('default', $config);
		$db = ConnectionManager::getDataSource('default');
		if (!$db->isConnected()) {
			$this->Session->setFlash(__('Could not connect to database.', true), 'default', array('class' => 'error'));
			return;
		}

		copy(CONFIGS.'database.php.install', CONFIGS.'database.php');
		App::import('Core', 'File');
		$file = new File(CONFIGS.'database.php', true);
		$content = $file->read();

		foreach ($config AS $configKey => $configValue) {
			$content = str_replace('{default_' . $configKey . '}', $configValue, $content);
		}

		if($file->write($content) ) {
			return $this->redirect(array('action' => 'data'));
		} else {
			$this->Session->setFlash(__('Could not write database.php file.', true), 'default', array('class' => 'error'));
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
		$this->set('title_for_layout', __('Step 2: Build database', true));
		if (isset($this->params['named']['run'])) {
			App::import('Core', 'File');
			App::import('Model', 'CakeSchema', false);
			App::import('Model', 'ConnectionManager');

			$db = ConnectionManager::getDataSource('default');
			if(!$db->isConnected()) {
				$this->Session->setFlash(__('Could not connect to database.', true), 'default', array('class' => 'error'));
			} else {
				$schema =& new CakeSchema(array('name'=>'app'));
				$schema = $schema->load();
				foreach($schema->tables as $table => $fields) {
					$create = $db->createSchema($schema, $table);
					$db->execute($create);
				}

				$dataObjects = App::objects('class', APP . 'plugins' . DS . 'install' . DS . 'config' . DS . 'data' . DS);
				foreach ($dataObjects as $data) {
					App::import('class', $data, false, APP . 'plugins' . DS . 'install' . DS . 'config' . DS . 'data' . DS);
					$classVars = get_class_vars($data);
					$modelAlias = substr($data, 0, -4);
					$table = $classVars['table'];
					$records = $classVars['records'];
					App::import('Model', 'Model', false);
					$modelObject =& new Model(array(
						'name' => $modelAlias,
						'table' => $table,
						'ds' => 'default',
					));
					if (is_array($records) && count($records) > 0) {
						foreach($records as $record) {
							$modelObject->create($record);
							$modelObject->save();
						}
					}
				}

				$this->redirect(array('action' => 'finish'));
			}
		}
	}

/**
 * Step 3: finish
 *
 * Remind the user to delete 'install' plugin
 * Copy settings.yml file into place
 *
 * @return void
 * @access public
 */
	public function finish() {
		$this->set('title_for_layout', __('Installation completed successfully', true));
		if (isset($this->params['named']['delete'])) {
			App::import('Core', 'Folder');
			$this->folder = new Folder;
			if ($this->folder->delete(APP.'plugins'.DS.'install')) {
				$this->Session->setFlash(__('Installation files deleted successfully.', true), 'default', array('class' => 'success'));
				$this->redirect('/');
			} else {
				return $this->Session->setFlash(__('Could not delete installation files.', true), 'default', array('class' => 'error'));
			}
		}
		$this->_check();

		// set new salt and seed value
		copy(CONFIGS.'settings.yml.install', CONFIGS.'settings.yml');
		$File =& new File(CONFIGS . 'core.php');
		if (!class_exists('Security')) {
			require LIBS . 'security.php';
		}
		$salt = Security::generateAuthKey();
		$seed = mt_rand() . mt_rand();
		$contents = $File->read();
		$contents = preg_replace('/(?<=Configure::write\(\'Security.salt\', \')([^\' ]+)(?=\'\))/', $salt, $contents);
		$contents = preg_replace('/(?<=Configure::write\(\'Security.cipherSeed\', \')(\d+)(?=\'\))/', $seed, $contents);
		if (!$File->write($contents)) {
			return false;
		}

		// set new password for admin, hashed according to new salt value
		$User = ClassRegistry::init('User');
		$User->id = $User->field('id', array('username' => 'admin'));
		$User->saveField('password', Security::hash('password', null, $salt));
	}

}
