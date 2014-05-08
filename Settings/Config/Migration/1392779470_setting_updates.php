<?php

App::uses('DataMigration', 'Extensions.Utility');

class ExposeSiteThemeAndLocaleAndHomeUrl extends CakeMigration {

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
				$settingsToUpdate = array(
					'Site.locale', 'Site.admin_theme', 'Site.home_url',
				);
				CakePlugin::load('Install');
				$dm = new DataMigration();
				$dir = CakePlugin::path('Install') . 'Config' . DS . 'Data' . DS;
				foreach ($settingsToUpdate as $key) {
					$dm->loadFile($dir . 'SettingData.php', array(
						'extract' => sprintf('{n}[key=%s]', $key)
					));
				}
				CakePlugin::unload('Install');
			}
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
