<?php
App::uses('File', 'Utility');

/**
 * Setting
 *
 * PHP version 5
 *
 * @category Model
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Setting extends AppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	public $name = 'Setting';

/**
 * Path to settings file
 *
 * @var string
 */
	public $settingsPath = '';

/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
	public $actsAs = array(
		'Ordered' => array(
			'field' => 'weight',
			'foreign_key' => false,
		),
		'Cached' => array(
			'prefix' => array(
				'setting_',
			),
		),
	);

/**
 * Validation
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'key' => array(
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'This key has already been taken.',
			),
			'minLength' => array(
				'rule' => array('minLength', 1),
				'message' => 'Key cannot be empty.',
			),
		),
	);

/**
 * __construct
 *
 * @param mixed $id
 * @param string $table
 * @param DataSource $ds
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->settingsPath = APP . 'Config' . DS . 'settings.yml';
	}
/**
 * afterSave callback
 *
 * @return void
 */
	public function afterSave($created) {
		$this->updateYaml();
		$this->writeConfiguration();
	}

/**
 * afterDelete callback
 *
 * @return void
 */
	public function afterDelete() {
		$this->updateYaml();
		$this->writeConfiguration();
	}

/**
 * Creates a new record with key/value pair if key does not exist.
 *
 * @param string $key
 * @param string $value
 * @param array $options
 * @return boolean
 */
	public function write($key, $value, $options = array()) {
		$setting = $this->findByKey($key);
		if (isset($setting['Setting']['id'])) {
			$setting['Setting']['id'] = $setting['Setting']['id'];
			$setting['Setting']['value'] = $value;

			$setting['Setting'] = $options + $setting['Setting'];

		} else {

			$options = array_merge(array(
				'title' => '',
				'description' => '',
				'input_type' => '',
				'editable' => 0,
				'params' => '',
			), $options);

			$setting = array();
			$setting['key'] = $key;
			$setting['value'] = $value;
			$setting['title'] = $options['title'];
			$setting['description'] = $options['description'];
			$setting['input_type'] = $options['input_type'];
			$setting['editable'] = $options['editable'];
			$setting['params'] = $options['params'];
		}

		$this->id = false;
		if ($this->save($setting)) {
			Configure::write($key, $value);
			return true;
		} else {
			return false;
		}
	}

/**
 * Deletes setting record for given key
 *
 * @param string $key
 * @return boolean
 */
	public function deleteKey($key) {
		$setting = $this->findByKey($key);
		if (isset($setting['Setting']['id']) &&
			$this->delete($setting['Setting']['id'])) {
			return true;
		}
		return false;
	}

/**
 * All key/value pairs are made accessible from Configure class
 *
 * @return void
 */
	public function writeConfiguration() {
		$settings = $this->find('all', array(
			'fields' => array(
				'Setting.key',
				'Setting.value',
			),
			'cache' => array(
				'name' => 'setting_write_configuration',
				'config' => 'setting_write_configuration',
			),
		));
		foreach ($settings as $setting) {
			Configure::write($setting['Setting']['key'], $setting['Setting']['value']);
		}
	}

/**
 * Find list and save yaml dump in app/config/settings.yml file.
 * Data required in bootstrap.
 *
 * @return void
 */
	public function updateYaml() {
		$list = $this->find('list', array(
			'fields' => array(
				'key',
				'value',
			),
			'order' => array(
				'Setting.key' => 'ASC',
			),
		));
		$file = new File($this->settingsPath, true);
		$listYaml = Spyc::YAMLDump($list, 4, 60);
		$file->write($listYaml);
	}
}
