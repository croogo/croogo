<?php

App::uses('ModelBehavior', 'Model');
App::uses('AppModel', 'Model');

/**
 * CroogoTranslate Behavior
 *
 * Modified version of cake's core TranslateBehavior.
 * If no translated record is found for the locale, the main record will be returned.
 * TranslateBehavior used to return nothing in that case.
 *
 * @category Behavior
 * @package  Croogo.Translate.Model.Behavior
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoTranslateBehavior extends ModelBehavior {

/**
 * Used for runtime configuration of model
 */
	public $runtime = array();

/**
 * Field names
 *
 * @var array
 */
	public $translationFields = array();

/**
 * Callback
 *
 * $config for CroogoTranslateBehavior should be
 * array( 'fields' => array('field_one',
 * 'field_two' => 'FieldAssoc', 'field_three'))
 *
 * With above example only one permanent hasMany will be joined (for field_two
 * as FieldAssoc)
 *
 * $config could be empty - and translations configured dynamically by
 * bindTranslation() method
 *
 * @param Model $model
 * @param array $config
 * @return mixed
 * @access public
 */
	public function setup(Model $model, $config = array()) {
		$db = ConnectionManager::getDataSource($model->useDbConfig);
		if (!$db->connected) {
			trigger_error(
				__d('croogo', 'Datasource %s for CroogoTranslateBehavior of model %s is not connected', $model->useDbConfig, $model->alias),
				E_USER_ERROR
			);
			return false;
		}

		$this->settings[$model->alias] = array();
		$this->runtime[$model->alias] = array('fields' => array());
		$this->translateModel($model);
		$this->translationFields[$model->alias] = $config['fields'];
		//return $this->bindTranslation($model, $config, false);
	}

/**
 * Callback
 *
 * @return void
 * @access public
 */
	public function cleanup(Model $model) {
		//$this->unbindTranslation($model);
		unset($this->settings[$model->alias]);
		unset($this->runtime[$model->alias]);
	}

/**
 * Get field names for Translation
 *
 * @param object $model
 * @return array
 * @access public
 */
	public function getTranslationFields(Model $model) {
		if (Hash::numeric(array_keys($this->translationFields[$model->alias]))) {
			return $this->translationFields[$model->alias];
		} else {
			return array_keys($this->translationFields[$model->alias]);
		}
	}

/**
 * afterFind Callback
 *
 * @param array $results
 * @param boolean $primary
 * @return array Modified results
 * @access public
 */
	public function afterFind(Model $model, $results, $primary) {
		$locale = $this->_getLocale($model);

		if (empty($locale) || empty($results)) {
			return $results;
		}

		$fields = $this->getTranslationFields($model);
		$RuntimeModel = $this->translateModel($model);

		if ($primary && isset($results[0][$model->alias])) {
			$i = 0;
			foreach ($results as $result) {
				if (!isset($result[$model->alias][$model->primaryKey])) {
					continue;
				}

				$translations = $RuntimeModel->find('all', array(
					'conditions' => array(
						$RuntimeModel->alias . '.model' => $model->alias,
						$RuntimeModel->alias . '.foreign_key' => $result[$model->alias][$model->primaryKey],
						$RuntimeModel->alias . '.field' => $fields,
					),
				));

				foreach ($translations as $translation) {
					$field = $translation[$RuntimeModel->alias]['field'];

					// Original row
					/*if (isset($results[$i][$model->alias][$field])) {
						$results[$i][$field.'Original'] = $results[$i][$model->alias][$field];
					}*/

					// Translated row
					if ($translation[$RuntimeModel->alias]['locale'] == $locale &&
						isset($results[$i][$model->alias][$field])) {
						$results[$i][$model->alias][$field] = $translation[$RuntimeModel->alias]['content'];
					}

					// Other translations
					if (!Hash::numeric(array_keys($this->translationFields[$model->alias])) &&
						isset($results[$i][$model->alias][$field])) {
						if (!isset($results[$i][$field . 'Translation'])) {
							$results[$i][$field . 'Translation'] = array();
						}
						$results[$i][$field . 'Translation'][] = $translation[$RuntimeModel->alias];
					}
				}

				$i++;
			}
		}

		return $results;
	}

/**
 * Save translation only (in i18n table)
 *
 * @param object $model
 * @param array $data
 * @param boolean $validate
 */
	public function saveTranslation(Model $model, $data = null, $validate = true) {
		$model->data = $data;
		if (!isset($model->data[$model->alias])) {
			return false;
		}

		$locale = $this->_getLocale($model);
		if (empty($locale)) {
			return false;
		}

		$RuntimeModel = $this->translateModel($model);
		$conditions = array('model' => $model->alias, 'foreign_key' => $model->id);

		foreach ($model->data[$model->alias] as $field => $value) {
			unset($conditions['content']);
			$conditions['field'] = $field;
			if (is_array($value)) {
				$conditions['locale'] = array_keys($value);
			} else {
				$conditions['locale'] = $locale;
				if (is_array($locale)) {
					$value = array($locale[0] => $value);
				} else {
					$value = array($locale => $value);
				}
			}
			$translations = $RuntimeModel->find('list', array('conditions' => $conditions, 'fields' => array($RuntimeModel->alias . '.locale', $RuntimeModel->alias . '.id')));
			foreach ($value as $_locale => $_value) {
				$RuntimeModel->create();
				$conditions['locale'] = $_locale;
				$conditions['content'] = $_value;
				if (array_key_exists($_locale, $translations)) {
					if (!$RuntimeModel->save(array($RuntimeModel->alias => array_merge($conditions, array('id' => $translations[$_locale]))))) {
						return false;
					}
				} else {
					if (!$RuntimeModel->save(array($RuntimeModel->alias => $conditions))) {
						return false;
					}
				}
			}
		}

		return true;
	}

/**
 * afterDelete Callback
 *
 * @return void
 * @access public
 */
	public function afterDelete(Model $model) {
		$RuntimeModel = $this->translateModel($model);
		$conditions = array('model' => $model->alias, 'foreign_key' => $model->id);
		$RuntimeModel->deleteAll($conditions);
	}

/**
 * Get selected locale for model
 *
 * @return mixed string or false
 * @access protected
 */
	protected function _getLocale(Model $model) {
		if (!isset($model->locale) || is_null($model->locale)) {
			/*
			if (!class_exists('I18n')) {
				App::import('Core', 'i18n');
			}
			$I18n = I18n::getInstance();
			$I18n->l10n->get(Configure::read('Config.language'));
			$model->locale = $I18n->l10n->locale;
			*/
			$model->locale = Configure::read('Config.language');
		}

		return $model->locale;
	}

/**
 * Get instance of model for translations
 *
 * @return object
 * @access public
 */
	public function &translateModel(Model $model) {
		if (!isset($this->runtime[$model->alias]['model'])) {
			if (!isset($model->translateModel) || empty($model->translateModel)) {
				$className = 'I18nModel';
			} else {
				$className = $model->translateModel;
			}

			$this->runtime[$model->alias]['model'] = ClassRegistry::init($className, 'Model');
		}
		if (!empty($model->translateTable) && $model->translateTable !== $this->runtime[$model->alias]['model']->useTable) {
			$this->runtime[$model->alias]['model']->setSource($model->translateTable);
		} elseif (empty($model->translateTable) && empty($model->translateModel)) {
			$this->runtime[$model->alias]['model']->setSource('i18n');
		}
		return $this->runtime[$model->alias]['model'];
	}

}

if (!defined('CAKEPHP_UNIT_TEST_EXECUTION')) {
/**
 * @package	 Croogo.Translate.Model.Behavior
 */
	class I18nModel extends AppModel {

		public $name = 'I18nModel';

		public $useTable = 'i18n';

		public $displayField = 'field';

		public $actsAs = array(
			'Croogo.Cached' => array(),
		);
	}

}
