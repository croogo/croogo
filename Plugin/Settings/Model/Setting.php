<?php

App::uses('AppModel', 'Model');
App::uses('File', 'Utility');

/**
 * Setting
 *
 * PHP version 5
 *
 * @category Model
 * @package  Croogo.Settings.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Setting extends SettingsAppModel {

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
		'Croogo.Ordered' => array(
			'field' => 'weight',
			'foreign_key' => false,
		),
		'Croogo.Cached' => array(
			'groups' => array(
				'settings',
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
		$this->settingsPath = APP . 'Config' . DS . 'settings.json';
	}
/**
 * afterSave callback
 *
 * @return void
 */
	public function afterSave($created) {
		$this->updateJson();
		$this->writeConfiguration();
	}

/**
 * afterDelete callback
 *
 * @return void
 */
	public function afterDelete() {
		$this->updateJson();
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
		Configure::load('settings', 'settings');
	}

/**
 * Find list and save yaml dump in app/Config/settings.json file.
 * Data required in bootstrap.
 *
 * @return void
 */
	public function updateJson() {
		$settings = $this->find('all', array(
			'fields' => array(
				'key',
				'value',
			),
			'order' => array(
				'Setting.key' => 'ASC',
			),
		));
		$settings = array_combine(
			Hash::extract($settings, '{n}.Setting.key'),
			Hash::extract($settings, '{n}.Setting.value')
			);
		Configure::write($settings);
		foreach ($settings as $key => $setting) {
			list($key, $ignore) = explode('.', $key, 2);
			$keys[] = $key;
		}
		$keys = array_unique($keys);
		Configure::dump('settings.json', 'settings', $keys);
	}

/**
 * beforeSave
 *
 * if 'values' is present, serialize it with json_encode and save it in 'value'.
 * this is used for allowing 'multiple' input_type (select|checkbox) feature
 */
	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['values'])) {
			$this->data[$this->alias]['value'] = json_encode($this->data[$this->alias]['values']);
		}
		return true;
	}

}
