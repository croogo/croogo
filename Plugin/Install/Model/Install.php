<?php

App::uses('InstallAppModel', 'Install.Model');
App::uses('CakeTime', 'Utility');
App::uses('Security', 'Utility');
App::uses('File', 'Utility');
App::uses('CroogoPlugin', 'Extensions.Lib');

class Install extends InstallAppModel {

/**
 * name
 *
 * @var string
 */
	public $name = 'Install';

/**
 * useTable
 *
 * @var string
 */
	public $useTable = false;

/**
 *
 * @var CroogoPlugin
 */
	protected $_CroogoPlugin = null;

/**
 * Create admin user
 *
 * @var array $user User datas
 * @return If user is created
 */
	public function addAdminUser($user) {
		if (!CakePlugin::loaded('Users')) {
			CakePlugin::load('Users');
		}
		$User = ClassRegistry::init('Users.User');
		$Role = ClassRegistry::init('Users.Role');
		$Role->Behaviors->attach('Croogo.Aliasable');
		unset($User->validate['email']);
		$user['User']['name'] = $user['User']['username'];
		$user['User']['email'] = '';
		$user['User']['timezone'] = 0;
		$user['User']['role_id'] = $Role->byAlias('admin');
		$user['User']['status'] = true;
		$user['User']['activation_key'] = md5(uniqid());
		$User->create();
		$saved = $User->save($user);
		if (!$saved) {
			$this->log('Unable to create administrative user. Validation errors:');
			$this->log($User->validationErrors);
		}
		return $saved;
	}

/**
 * Run Migrations and add data in table
 *
 * @return If migrations have succeeded
 */
	public function setupDatabase() {
		$plugins = Configure::read('Core.corePlugins');

		$migrationsSucceed = true;
		foreach ($plugins as $plugin) {
			$migrationsSucceed = $this->runMigrations($plugin);
			if (!$migrationsSucceed) {
				break;
			}
		}

		if ($migrationsSucceed) {
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
				ClassRegistry::removeObject($modelAlias);
			}
		}

		return $migrationsSucceed;
	}

	public function runMigrations($plugin) {
		if (!CakePlugin::loaded($plugin)) {
			CakePlugin::load($plugin);
		}
		return $this->_getCroogoPlugin()->migrate($plugin);
	}

	protected function _getCroogoPlugin() {
		if (!($this->_CroogoPlugin instanceof CroogoPlugin)) {
			$this->_setCroogoPlugin(new CroogoPlugin());
		}
		return $this->_CroogoPlugin;
	}

	protected function _setCroogoPlugin($croogoPlugin) {
		unset($this->_CroogoPlugin);
		$this->_CroogoPlugin = $croogoPlugin;
	}
}
