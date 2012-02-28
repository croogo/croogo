<?php

App::uses('InstallAppModel', 'Install.Model');

class Install extends InstallAppModel {

    public $name = 'Install';
    public $useTable = false;

/** Finalize installation
 *  Prepares Config/settings.yml and update password for admin user
 *  @return $mixed if false, indicates processing failure
 */
	public function finalize() {
        if (Configure::read('Install.installed') && Configure::read('Install.secured')) {
            return false;
        }
        // set new salt and seed value
        copy(APP . 'Config' . DS.'settings.yml.install', APP . 'Config' . DS.'settings.yml');
        App::uses('File', 'Utility');
        $File =& new File(APP . 'Config' . DS . 'core.php');
        if (!class_exists('Security')) {
            require CAKE . 'Utility' .DS. 'Security.php';
        }
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

        // set default password for admin
        $User = ClassRegistry::init('User');
        $User->id = $User->field('id', array('username' => 'admin'));
        return $User->saveField('password', 'password');
	}

}