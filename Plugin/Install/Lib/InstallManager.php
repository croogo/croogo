<?php

class InstallManager {

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

	public function createDatabaseFile($data) {
		App::uses('File', 'Utility');
		App::uses('ConnectionManager', 'Model');
		$config = $this->defaultConfig;

		foreach ($data['Install'] as $key => $value) {
			if (isset($data['Install'][$key])) {
				$config[$key] = $value;
			}
		}

		copy(APP . 'Config' . DS . 'database.php.install', APP . 'Config' . DS . 'database.php');
		$file = new File(APP . 'Config' . DS . 'database.php', true);
		$content = $file->read();

		foreach ($config as $configKey => $configValue) {
			$content = str_replace('{default_' . $configKey . '}', $configValue, $content);
		}

		if (!$file->write($content)) {
			return __d('croogo', 'Could not write database.php file.');
		}

		try {
			ConnectionManager::create('default', $config);
			$db = ConnectionManager::getDataSource('default');
		}
		catch (MissingConnectionException $e) {
			return __d('croogo', 'Could not connect to database: ') . $e->getMessage();
		}
		if (!$db->isConnected()) {
			return __d('croogo', 'Could not connect to database.');
		}

		return true;
	}

	public function createCroogoFile() {
		$croogoConfigFile = APP . 'Config' . DS . 'croogo.php';
		copy($croogoConfigFile . '.install', $croogoConfigFile);
		$File =& new File($croogoConfigFile);
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

		return true;
	}

/**
 * Create settings.json from default file
 *
 * @return bool true when successful
 */
	public function createSettingsFile() {
		return copy(APP . 'Config' . DS . 'settings.json.install', APP . 'Config' . DS . 'settings.json');
	}

/**
 * Mark installation as complete
 *
 * @return bool true when successful
 */
	public function installCompleted() {
		$Setting = ClassRegistry::init('Settings.Setting');
		$Setting->Behaviors->disable('Cached');
		return $Setting->write('Croogo.installed', 1);
	}

}