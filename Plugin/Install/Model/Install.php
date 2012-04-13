<?php

App::uses('InstallAppModel', 'Install.Model');
App::uses('CakeTime', 'Utility');
App::uses('Security', 'Utility');
App::uses('File', 'Utility');

class Install extends InstallAppModel {

	public $name = 'Install';
	public $useTable = false;

/** Finalize installation
 *  Prepares Config/settings.yml and update password for admin user
 *  @return $mixed if false, indicates processing failure
 */
	public function finalize($user) {
		if (Configure::read('Install.installed') && Configure::read('Install.secured')) {
			return false;
		}
		copy(APP . 'Config' . DS.'settings.yml.install', APP . 'Config' . DS.'settings.yml');

		// set new salt and seed value
		if (!Configure::read('Install.secured')) {
			$File =& new File(APP . 'Config' . DS . 'croogo.php');
			$salt = Security::generateAuthKey();
			$seed = mt_rand() . mt_rand();
			$contents = $File->read();
			$contents = preg_replace('/(?<=Configure::write\(\'Security.salt\', \')([^\' ]+)(?=\'\))/', $salt, $contents);
			$contents = preg_replace('/(?<=Configure::write\(\'Security.cipherSeed\', \')(\d+)(?=\'\))/', $seed, $contents);
			if (!$File->write($contents)) {
				return false;
			}
			Configure::write('Security.salt', $salt);
			Configure::write('Security.cipherSeed', $seed);
		}

		// create administrative user
		$User = ClassRegistry::init('User');
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
		}
		return $saved;
	}

}