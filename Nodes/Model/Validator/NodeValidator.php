<?php
App::uses('CroogoAppModelValidator', 'Croogo.Model/Validator');
class NodeValidator extends CroogoAppModelValidator {

	public $customValidationRules = array('isUniquePerType');
	public function __construct(Model $Model) {
		parent::__construct($Model);

		$this->add('title', 'notempty',  array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => __d('croogo', 'Title cannot be empty.'),
		));

		$this->add('slug', 'notempty',  array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => __d('croogo', 'Slug cannot be empty.'),
		));

		$this->add('slug', 'isUniquePerType',  array(
			'rule' => 'isUniquePerType',
			'required' => true,
			'message' => __d('croogo', 'This slug has already been taken.'),
		));
	}

/**
 * Returns false if any fields passed match any (by default, all if $or = false) of their matching values.
 *
 * @param array $fields Field/value pairs to search (if no values specified, they are pulled from $this->data)
 * @param boolean $or If false, all fields specified must match in order for a false return value
 * @return boolean False if any records matching any fields are found
 * @access public
 */
	public function isUniquePerType($fields, $or = true) {
		if (!is_array($fields)) {
			$fields = func_get_args();
			if (is_bool($fields[count($fields) - 1])) {
				$or = $fields[count($fields) - 1];
				unset($fields[count($fields) - 1]);
			}
		}

		$modelData = $this->getModel()->data;
		$modelAlias = $this->getModel()->alias;
		$modelType = $this->getModel()->type;
		$modelId = $this->getModel()->id;

		foreach ($fields as $field => $value) {
			if (is_numeric($field)) {
				unset($fields[$field]);

				$field = $value;
				if (isset($modelData[$modelAlias][$field])) {
					$value = $modelData[$modelAlias][$field];
				} else {
					$value = null;
				}
			}

			if (strpos($field, '.') === false) {
				unset($fields[$field]);
				$fields[$modelAlias . '.' . $field] = $value;
			}
		}
		if ($or) {
			$fields = array('or' => $fields);
		}
		if (!empty($modelId)) {
			$fields[$modelAlias . '.' . $this->getModel()->primaryKey . ' !='] = $modelId;
		}
		if (!empty($modelType)) {
			$fields[$modelAlias . '.type'] = $modelType;
		}
		return ($this->getModel()->find('count', array('conditions' => $fields, 'recursive' => -1)) == 0);
	}
}
