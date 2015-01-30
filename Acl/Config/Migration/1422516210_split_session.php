<?php

App::uses('DataMigration', 'Extensions.Utility');

class SplitSession extends CakeMigration {

	public $migration = array(
		'up' => array(),
		'down' => array(),
	);

	public function after($direction) {
		$success = true;
		$Setting = ClassRegistry::init('Settings.Setting');

		$key = 'Access Control.splitSession';

		if ($direction === 'up') {
			if (Configure::read('Croogo.installed')) {
				CakePlugin::load('Install');
				$dm = new DataMigration();
				$dir = CakePlugin::path('Install') . 'Config' . DS . 'Data' . DS;
				$dm->loadFile($dir . 'SettingData.php', array(
					'extract' => sprintf('{n}[key=%s]', $key),
				));
				CakePlugin::unload('Install');
			}
		} else {
			$Setting = ClassRegistry::init('Settings.Setting');
			$success = $Setting->deleteKey($key);
		}

		return $success;
	}

}
