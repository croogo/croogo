<?php
/* SVN FILE: $Id$ */
/**
 * Short description for file.
 *
 * Lond description for file.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.model.behaviors
 * @since         CakePHP(tm) v 1.2.0.4525
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * CroogoTranslate Behavior
 *
 * Modified version of cake's core TranslateBehavior.
 * If no translated record is found for the locale, the main record will be returned.
 * TranslateBehavior used to return nothing in that case.
 *
 * @category Behavior
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoTranslateBehavior extends ModelBehavior {
/**
 * Used for runtime configuration of model
 */
	var $runtime = array();
/**
 * Field names
 *
 * @var array
 */
    var $translationFields = array();
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
 * @param array $config
 * @return mixed
 * @access public
 */
	function setup(&$model, $config = array()) {
		$db =& ConnectionManager::getDataSource($model->useDbConfig);
		if (!$db->connected) {
			trigger_error(
				sprintf(__('Datasource %s for CroogoTranslateBehavior of model %s is not connected', true), $model->useDbConfig, $model->alias),
				E_USER_ERROR
			);
			return false;
		}

		$this->settings[$model->alias] = array();
		$this->runtime[$model->alias] = array('fields' => array());
		$this->translateModel($model);
        $this->translationFields = $config;
		//return $this->bindTranslation($model, $config, false);
	}
/**
 * Callback
 *
 * @return void
 * @access public
 */
	function cleanup(&$model) {
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
    function getTranslationFields(&$model) {
        if (Set::numeric(array_keys($this->translationFields))) {
            return $this->translationFields;
        } else {
            return array_keys($this->translationFields);
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
    function afterFind(&$model, $results, $primary) {
		$locale = $this->_getLocale($model);

		if (empty($locale) || empty($results)) {
			return $results;
		}
        
        $fields = $this->getTranslationFields($model);
        $RuntimeModel =& $this->translateModel($model);

        if ($primary && isset($results[0][$model->alias])) {
            $i = 0;
            foreach ($results AS $result) {
                if (!isset($result[$model->alias][$model->primaryKey])) continue;

                $translations = $RuntimeModel->find('all', array(
                    'conditions' => array(
                        $RuntimeModel->alias.'.model' => $model->alias,
                        $RuntimeModel->alias.'.foreign_key' => $result[$model->alias][$model->primaryKey],
                        $RuntimeModel->alias.'.field' => $fields,
                    ),
                ));

                foreach ($translations AS $translation) {
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
                    if (!Set::numeric(array_keys($this->translationFields)) &&
                        isset($results[$i][$model->alias][$field])) {
                        if (!isset($results[$i][$field.'Translation'])) {
                            $results[$i][$field.'Translation'] = array();
                        }
                        $results[$i][$field.'Translation'][] = $translation[$RuntimeModel->alias];
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
    function saveTranslation(&$model, $data = null, $validate = true) {
        $model->data = $data;

        // beforeValidate of TranslateBehavior
		$locale = $this->_getLocale($model);
		if (empty($locale)) {
			return true;
		}
		$fields = array_merge($this->settings[$model->alias], $this->runtime[$model->alias]['fields']);
		$tempData = array();

		foreach ($fields as $key => $value) {
			$field = (is_numeric($key)) ? $value : $key;

			if (isset($model->data[$model->alias][$field])) {
				$tempData[$field] = $model->data[$model->alias][$field];
				if (is_array($model->data[$model->alias][$field])) {
					if (is_string($locale) && !empty($model->data[$model->alias][$field][$locale])) {
						$model->data[$model->alias][$field] = $model->data[$model->alias][$field][$locale];
					} else {
						$values = array_values($model->data[$model->alias][$field]);
						$model->data[$model->alias][$field] = $values[0];
					}
				}
			}
		}
		$this->runtime[$model->alias]['beforeSave'] = $tempData;

        // afterSave of TranslateBehavior
        $locale = $this->_getLocale($model);
		$tempData = $this->runtime[$model->alias]['beforeSave'];
		unset($this->runtime[$model->alias]['beforeSave']);
		$conditions = array('model' => $model->alias, 'foreign_key' => $model->id);
		$RuntimeModel =& $this->translateModel($model);

		foreach ($tempData as $field => $value) {
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
					$RuntimeModel->save(array($RuntimeModel->alias => array_merge($conditions, array('id' => $translations[$_locale]))));
				} else {
					$RuntimeModel->save(array($RuntimeModel->alias => $conditions));
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
	function afterDelete(&$model) {
		$RuntimeModel =& $this->translateModel($model);
		$conditions = array('model' => $model->alias, 'foreign_key' => $model->id);
		$RuntimeModel->deleteAll($conditions);
	}
/**
 * Get selected locale for model
 *
 * @return mixed string or false
 * @access protected
 */
	function _getLocale(&$model) {
		if (!isset($model->locale) || is_null($model->locale)) {
			/*
            if (!class_exists('I18n')) {
				App::import('Core', 'i18n');
			}
			$I18n =& I18n::getInstance();
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
	function &translateModel(&$model) {
		if (!isset($this->runtime[$model->alias]['model'])) {
			if (!isset($model->translateModel) || empty($model->translateModel)) {
				$className = 'I18nModel';
			} else {
				$className = $model->translateModel;
			}

			if (PHP5) {
				$this->runtime[$model->alias]['model'] = ClassRegistry::init($className, 'Model');
			} else {
				$this->runtime[$model->alias]['model'] =& ClassRegistry::init($className, 'Model');
			}
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
 * @package       cake
 * @subpackage    cake.cake.libs.model.behaviors
 */
	class I18nModel extends AppModel {
		var $name = 'I18nModel';
		var $useTable = 'i18n';
		var $displayField = 'field';
	}
}
?>
