<?php

namespace Croogo\Settings\Config\Migration;
App::uses('DataMigration', 'Extensions.Utility');

class AddedAssetTimestampSetting extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
	public $description = '';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
	public $migration = array(
		'up' => array(
		),
		'down' => array(
		),
	);

	protected $_assetTimestamp = 'Site.asset_timestamp';

/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function before($direction) {
		$success = true;

		if ($direction === 'up') {
			if (Configure::read('Croogo.installed')) {
				CakePlugin::load('Install');
				$dm = new DataMigration();
				$dir = CakePlugin::path('Install') . 'Config' . DS . 'Data' . DS;
				$dm->loadFile($dir . 'SettingData.php', array(
					'extract' => sprintf('{n}[key=%s]',$this->_assetTimestamp),
				));
				CakePlugin::unload('Install');
			}
		} else {
			$Setting = ClassRegistry::init('Settings.Setting');
			$success = $Setting->deleteKey($this->_assetTimestamp);
		}

		return $success;
	}

/**
 * After migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function after($direction) {
		return true;
	}
}
