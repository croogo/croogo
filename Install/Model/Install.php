<?php

App::uses('CakeTime', 'Utility');
App::uses('CroogoPlugin', 'Extensions.Lib');
App::uses('DataMigration', 'Extensions.Lib/Utility');
App::uses('File', 'Utility');
App::uses('InstallAppModel', 'Install.Model');
App::uses('Security', 'Utility');

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
 * @return bool True if migrations have succeeded
 */
	public function setupDatabase() {
		$plugins = Configure::read('Core.corePlugins');

		$migrationsSucceed = true;
		foreach ($plugins as $plugin) {
			$migrationsSucceed = $this->runMigrations($plugin);
			if (!$migrationsSucceed) {
				$this->log('Migrations failed for ' . $plugin, LOG_CRIT);
				break;
			}
		}

		if ($migrationsSucceed) {
			$DataMigration = new DataMigration();
			$path = App::pluginPath('Install') . DS . 'Config' . DS . 'Data' . DS;
			$DataMigration->load($path);
		}

		return $migrationsSucceed;
	}

	public function runMigrations($plugin) {
		if (!CakePlugin::loaded($plugin)) {
			CakePlugin::load($plugin);
		}
		$CroogoPlugin = $this->_getCroogoPlugin();
		$result = $CroogoPlugin->migrate($plugin);
		if (!$result) {
			$this->log($CroogoPlugin->migrationErrors);
		}
		return $result;
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
