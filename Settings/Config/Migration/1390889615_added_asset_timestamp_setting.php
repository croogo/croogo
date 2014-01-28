<?php
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

	private $__assetTimestampSetting = array(); // initialized in constructor

	public function __construct($options = array()) {
		$this->__assetTimestampSetting = array(
			'key' => 'Site.asset_timestamp',
			'value' => 'force',
			'title' => 'Asset timestamp',
			'description' => implode('<br />', array(
				'Appends a timestamp which is last modified time of the particular file at the end of asset files URLs (CSS, JavaScript, Image).',
				'Useful to prevent visitors to visit the site with an outdated version of these files in their browser cache.'
			)),
			'editable' => 1,
			'input_type' => 'radio',
			'params' => 'options=' . json_encode(array(
				'0' => 'Disabled',
				'1' => 'Enabled in debug mode only',
				'force' => 'Always enabled',
			))
		);
		parent::__construct($options);
	}

/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function before($direction) {
		$success = true;

		$Setting = ClassRegistry::init('Settings.Setting');
		if ($direction === 'up') {
			$success = $Setting->write(
				$this->__assetTimestampSetting['key'],
				$this->__assetTimestampSetting['value'],
				$this->__assetTimestampSetting
			);
		} else {
			$success = $Setting->deleteKey($this->__assetTimestampSetting['key']);
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
