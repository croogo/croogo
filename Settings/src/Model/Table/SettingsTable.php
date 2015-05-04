<?php

namespace Croogo\Settings\Model\Table;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\Query;
use Croogo\Croogo\Model\Table\CroogoTable;

/**
 * Setting
 *
 * @category Model
 * @package  Croogo.Settings.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class SettingsTable extends CroogoTable {

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
		'Croogo.Trackable',
		'Search.Searchable',
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
 * Filter search fields
 */
	public $filterArgs = array(
		'key' => array('type' => 'like', 'field' => 'Settings.key'),
	);

	/**
	 * @param array $config
     */
	public function initialize(array $config) {
		parent::initialize($config);

		$this->addBehavior('Search.Searchable');

		$this->settingsPath = APP . 'config' . DS . 'settings.json';
	}


/**
 * beforeSave callback
 */
	public function beforeSave() {
		$this->connection()->driver()->autoQuoting(true);
	}

/**
 * afterSave callback
 */
	public function afterSave() {
		$this->connection()->driver()->autoQuoting(false);

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
		$setting = $this->findByKey($key)->first();
		if ($setting) {
			$setting->value = $value;

			$setting = $this->patchEntity($setting, $options);

		} else {

			$options = array_merge(array(
				'title' => '',
				'description' => '',
				'input_type' => '',
				'editable' => 0,
				'weight' => 0,
				'params' => '',
			), $options);

			$setting = $this->newEntity([
				'key' => $key,
				'value' => $value,
				'title' => $options['title'],
				'description' => $options['description'],
				'input_type' => $options['input_type'],
				'editable' => $options['editable'],
				'weight' => $options['weight'],
				'params' => $options['params'],
			]);
		}

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
				'Settings.key' => 'ASC',
			),
		));

		$settings = array_combine(
			collection($settings)->extract('key')->toArray(),
			collection($settings)->extract('value')->toArray()
		);
		Configure::write($settings);
		foreach ($settings as $key => $setting) {
			list($key, $ignore) = explode('.', $key, 2);
			$keys[] = $key;
		}
		$keys = array_unique($keys);
		Configure::dump('settings.json', 'settings', $keys);
	}
}
