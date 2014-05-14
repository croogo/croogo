<?php

App::uses('Croogo', 'Croogo.Lib');
App::uses('ModelBehavior', 'Model');

/**
 * Copyable Behavior class file.
 *
 * Adds ability to copy a model record, including all hasMany and
 * hasAndBelongsToMany associations. Relies on Containable behavior, which
 * this behavior will attach on the fly as needed.
 *
 * HABTM relationships are just duplicated in the join table, while hasMany
 * and hasOne records are recursively copied as well.
 *
 * Usage is straightforward:
 * From model: $this->copy($id); // id = the id of the record to be copied
 * From container: $this->MyModel->copy($id);
 *
 * @category Behavior
 * @package Croogo.Croogo.Model.Behavior
 * @author Jamie Nay
 * @copyright Jamie Nay
 * @license	http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link http://github.com/jamienay/copyable_behavior
 * @link http://www.croogo.org
 */
class CopyableBehavior extends ModelBehavior {

/**
 * Behavior settings
 */
	public $settings = array();

/**
 * Array of contained models.
 */
	public $contain = array();

/**
 * The full results of Model::find() that are modified and saved
 * as a new copy.
 */
	public $record = array();

/**
 * Default values for settings.
 *
 * - recursive: whether to copy hasMany and hasOne records
 * - habtm: whether to copy hasAndBelongsToMany associations
 * - stripFields: fields to strip during copy process
 * - ignore: aliases of any associations that should be ignored, using dot (.) notation.
 * will look in the $this->contain array.
 */
	protected $_defaults = array(
		'recursive' => false,
		'habtm' => false,
		'autoFields' => array(
			'title',
			'slug',
			'alias',
		),
		'stripFields' => array(
			'id',
			'created',
			'modified',
			'updated',
			'lft',
			'status',
			'rght'
		),
		'ignore' => array(
		),
		'masterKey' => null
	);

/**
 * Configuration method.
 *
 * @param object $Model Model object
 * @param array $settings Config array
 * @return boolean
 */
	public function setup(Model $Model, $settings = array()) {
		$this->settings[$Model->alias] = array_merge($this->_defaults, $settings);
		return true;
	}

/**
 * Copy method.
 *
 * @param object $Model model object
 * @param mixed $id String or integer model ID
 * @return boolean
 */
	public function copy(Model $Model, $id) {
		$this->generateContain($Model);
		$this->record = $Model->find('first', array(
			'conditions' => array(
				$Model->escapeField() => $id
			),
			'contain' => $this->contain
		));

		if (empty($this->record)) {
			return false;
		}

		if (!$this->_convertData($Model)) {
			return false;
		}

		$result = false;
		try {
			$result = $this->_copyRecord($Model);
		} catch (PDOException $e) {
			$this->log('Error executing _copyRecord: ' . $e->getMessage());
		}
		return $result;
	}

/**
 * Wrapper method that combines the results of _recursiveChildContain()
 * with the models' HABTM associations.
 *
 * @param object $Model Model object
 * @return array
 */
	public function generateContain(Model $Model) {
		if (!$this->_verifyContainable($Model)) {
			return false;
		}

		$this->contain = array_merge($this->_recursiveChildContain($Model), array_keys($Model->hasAndBelongsToMany));
		$this->_removeIgnored($Model);
		return $this->contain;
	}

/**
 * Removes any ignored associations, as defined in the model settings, from
 * the $this->contain array.
 *
 * @param object $Model Model object
 * @return boolean
 */
	protected function _removeIgnored(Model $Model) {
		if (!$this->settings[$Model->alias]['ignore']) {
			return true;
		}
		$ignore = array_unique($this->settings[$Model->alias]['ignore']);
		foreach ($ignore as $path) {
			if (Hash::check($this->contain, $path)) {
				$this->contain = Hash::remove($this->contain, $path);
			}
		}
		return true;
	}

/**
 * Strips primary keys and other unwanted fields
 * from hasOne and hasMany records.
 *
 * @param object $Model model object
 * @param array $record
 * @return array $record
 */
	protected function _convertChildren(Model $Model, $record) {
		$children = array_merge($Model->hasMany, $Model->hasOne);
		foreach ($children as $key => $val) {
			if (!isset($record[$key])) {
				continue;
			}

			if (empty($record[$key])) {
				unset($record[$key]);
				continue;
			}

			if (isset($record[$key][0])) {
				foreach ($record[$key] as $innerKey => $innerVal) {
					$record[$key][$innerKey] = $this->_stripFields($Model, $innerVal);
					if (array_key_exists($val['foreignKey'], $innerVal)) {
						unset($record[$key][$innerKey][$val['foreignKey']]);
					}

					$record[$key][$innerKey] = $this->_convertChildren($Model->{$key}, $record[$key][$innerKey]);
				}
			} else {
				$record[$key] = $this->_stripFields($Model, $record[$key]);

				if (isset($record[$key][$val['foreignKey']])) {
					unset($record[$key][$val['foreignKey']]);
				}

				$record[$key] = $this->_convertChildren($Model->{$key}, $record[$key]);
			}
		}

		return $record;
	}

/**
 * Strips primary and parent foreign keys (where applicable)
 * from $this->record in preparation for saving.
 *
 * When `autoFields` is set, it will iterate listed fields and append
 * ' (copy)' for titles or '-copy' for slug/alias fields.
 *
 * Plugins can also perform custom/additional data conversion by listening
 * on `Behavior.Copyable.convertData`
 *
 * @param object $Model Model object
 * @return array $this->record
 */
	protected function _convertData(Model $Model) {
		$this->record[$Model->alias] = $this->_stripFields($Model, $this->record[$Model->alias]);
		$this->record = $this->_convertHabtm($Model, $this->record);
		$this->record = $this->_convertChildren($Model, $this->record);

		if (!empty($this->settings[$Model->alias]['autoFields'])) {
			$autoFields = (array)$this->settings[$Model->alias]['autoFields'];
			$slugFields = array('slug', 'alias');
			foreach ($autoFields as $field) {
				if (!$Model->hasField($field)) {
					continue;
				}
				if (in_array($field, $slugFields)) {
					$this->record[$Model->alias][$field] .= '-copy';
				} else {
					$this->record[$Model->alias][$field] .= ' (copy)';
				}
			}
		}

		$eventName = 'Behavior.Copyable.convertData';
		$event = Croogo::dispatchEvent($eventName, $Model, array(
			'record' => $this->record,
		));

		$this->record = $event->data['record'];
		return $this->record;
	}

/**
 * Loops through any HABTM results in $this->record and plucks out
 * the join table info, stripping out the join table primary
 * key and the primary key of $Model. This is done instead of
 * a simple collection of IDs of the associated records, since
 * HABTM join tables may contain extra information (sorting
 * order, etc).
 *
 * @param Model $Model Model object
 * @param array $record
 * @return array modified $record
 */
	protected function _convertHabtm(Model $Model, $record) {
		if (!$this->settings[$Model->alias]['habtm']) {
			return $record;
		}
		foreach ($Model->hasAndBelongsToMany as $key => $val) {
			$className = pluginSplit($val['className']);
			$className = $className[1];
			if (!isset($record[$className]) || empty($record[$className])) {
				continue;
			}

			$joinInfo = Hash::extract($record[$className], '{n}.' . $val['with']);
			if (empty($joinInfo)) {
				continue;
			}

			foreach ($joinInfo as $joinKey => $joinVal) {
				$joinInfo[$joinKey] = $this->_stripFields($Model, $joinVal);

				if (array_key_exists($val['foreignKey'], $joinVal)) {
					unset($joinInfo[$joinKey][$val['foreignKey']]);
				}
			}

			$record[$className] = $joinInfo;
		}

		return $record;
	}

/**
 * Performs the actual creation and save.
 *
 * @param object $Model Model object
 * @return mixed
 */
	protected function _copyRecord(Model $Model) {
		$Model->create();

		$saved = $Model->saveAll($this->record, array(
			'validate' => false,
			'deep' => true
		));

		if ($this->settings[$Model->alias]['masterKey']) {
			$record = $this->_updateMasterKey($Model);
			$Model->saveAll($record, array(
				'validate' => false,
				'deep' => true
			));
		}
		return $saved;
	}

/**
 * Runs through to update the master key for deep copying.
 *
 * @param Model $Model
 * @return array
 */
	protected function _updateMasterKey(Model $Model) {
		$record = $Model->find('first', array(
			'conditions' => array(
				$Model->escapeField() => $Model->id
			),
			'contain' => $this->contain
		));

		$record = $this->_masterKeyLoop($Model, $record, $Model->id);
		return $record;
	}

/**
 * Called by _updateMasterKey as part of the copying process for deep recursion.
 *
 * @param Model $Model
 * @param array $record
 * @param integer $id
 * @return array
 */
	protected function _masterKeyLoop(Model $Model, $record, $id) {
		foreach ($record as $key => $val) {
			if (is_array($val)) {
				if (empty($val)) {
					unset($record[$key]);
				}
				foreach ($val as $innerKey => $innerVal) {
					if (is_array($innerVal)) {
						$record[$key][$innerKey] = $this->_masterKeyLoop($Model, $innerVal, $id);
					}
				}
			}

			if (!isset($val[$this->settings[$Model->alias]['masterKey']])) {
				continue;
			}

			$record[$this->settings[$Model->alias]['masterKey']] = $id;
		}
		return $record;
	}

/**
 * Generates a contain array for Containable behavior by
 * recursively looping through $Model->hasMany and
 * $Model->hasOne associations.
 *
 * @param object $Model Model object
 * @return array
 */
	protected function _recursiveChildContain(Model $Model) {
		$contain = array();
		if (!isset($this->settings[$Model->alias]) || !$this->settings[$Model->alias]['recursive']) {
			return $contain;
		}

		$children = array_merge(array_keys($Model->hasMany), array_keys($Model->hasOne));
		foreach ($children as $child) {
			if ($Model->alias == $child) {
				continue;
			}
			$contain[$child] = $this->_recursiveChildContain($Model->{$child});
		}

		return $contain;
	}

/**
 * Strips unwanted fields from $record, taken from
 * the 'stripFields' setting.
 *
 * @param object $Model Model object
 * @param array $record
 * @return array
 */
	protected function _stripFields(Model $Model, $record) {
		foreach ($this->settings[$Model->alias]['stripFields'] as $field) {
			if (array_key_exists($field, $record)) {
				unset($record[$field]);
			}
		}

		return $record;
	}

/**
 * Attaches Containable if it's not already attached.
 *
 * @param object $Model Model object
 * @return boolean
 */
	protected function _verifyContainable(Model $Model) {
		if (!$Model->Behaviors->attached('Containable')) {
			return $Model->Behaviors->attach('Containable');
		}

		return true;
	}

}
