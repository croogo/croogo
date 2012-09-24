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
 * Finalize installation
 *
 * Prepares Config/settings.json and update password for admin user
 * @param $user array user to create
 * @return $mixed if false, indicates processing failure
 */
	public function finalize($user) {
		if (Configure::read('Install.installed') && Configure::read('Install.secured')) {
			return false;
		}
		copy(APP . 'Config' . DS . 'settings.json.install', APP . 'Config' . DS . 'settings.json');

		// set new salt and seed value
		if (!Configure::read('Install.secured')) {
			$File =& new File(APP . 'Config' . DS . 'croogo.php');
			$salt = Security::generateAuthKey();
			$seed = mt_rand() . mt_rand();
			$contents = $File->read();
			$contents = preg_replace('/(?<=Configure::write\(\'Security.salt\', \')([^\' ]+)(?=\'\))/', $salt, $contents);
			$contents = preg_replace('/(?<=Configure::write\(\'Security.cipherSeed\', \')(\d+)(?=\'\))/', $seed, $contents);
			if (!$File->write($contents)) {
				$this->log('Unable to write your Config' . DS . 'croogo.php file. Please check the permissions.');
				return false;
			}
			Configure::write('Security.salt', $salt);
			Configure::write('Security.cipherSeed', $seed);
		}

		// create administrative user
		if (!CakePlugin::loaded('Users')) {
			CakePlugin::load('Users');
		}
		$User = ClassRegistry::init('Users.User');
		$User->Role->Behaviors->attach('Aliasable');
		unset($User->validate['email']);
		$user['User']['name'] = $user['User']['username'];
		$user['User']['email'] = '';
		$user['User']['timezone'] = 0;
		$user['User']['role_id'] = $User->Role->byAlias('admin');
		$user['User']['status'] = true;
		$user['User']['activation_key'] = md5(uniqid());
		$data = $User->create($user['User']);
		$saved = $User->save($data);
		if (!$saved) {
			$this->log('Unable to create administrative user. Validation errors:');
			$this->log($User->validationErrors);
			copy(APP . 'Config' . DS . 'croogo.php.install', APP . 'Config' . DS . 'croogo.php');
		}
		return $saved;
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
