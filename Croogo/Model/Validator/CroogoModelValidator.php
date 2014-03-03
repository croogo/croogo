<?php
App::uses('ModelValidator', 'Model');
class CroogoModelValidator extends ModelValidator {
    public $customValidationRules = array('validAlias', 'validName');

    /**
     * Utility function allowing merging model custom validation rule with preset ones.
     * @return array Validation rules
     */
    public function getMethods() {
		$methods = parent::getMethods();
		if (!empty($this->customValidationRules)) {
			foreach ($this->customValidationRules as $ruleName) {
				$methods[strtolower($ruleName)] = array($this, $ruleName);
			}
		}
		return $methods;
	}

/**
 * Validation method for alias field
 *
 * @return bool true when validation successful
 */
	public function validAlias($check) {
		return (preg_match('/^[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}-_]+$/mu', $check[key($check)]) == 1);
	}

/**
 * Validation method for name or title fields
 *
 * @return bool true when validation successful
 */
	public function validName($check) {
		return (preg_match('/^[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}-_\[\]\(\) ]+$/mu', $check[key($check)]) == 1);
	}
}
