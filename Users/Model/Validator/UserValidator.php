<?php
App::uses('CroogoModelValidator', 'Croogo.Model/Validator');
class UserValidator extends CroogoModelValidator {


	public function __construct(Model $Model) {
		parent::__construct($Model);
 		$this->customValidationRules = array_merge($this->customValidationRules, array('validIdentical'));

		$this->add('username', array(
			'notempty' =>  array(
				'rule' => array('notEmpty'),
				'required' => true,
				'message' => __d('croogo', 'Username cannot be empty.'),
			),
			'unique' => array(
				'rule' => array('isUnique'),
				'required' => true,
				'message' => __d('croogo', 'The username has already been taken.'),
			),
			'validalias' =>  array(
				'rule' => array('validAlias'),
				'required' => true,
				'message' => __d('croogo', 'This field must be alphanumeric.'),
			)
		));

		$this->add('email', array(
			'email' => array(
				'rule' => array('email'),
				'message' => __d('croogo', 'Please provide a valid email address.'),
				'last' => true,
			),
			'unique' => array(
				'rule' => array('isUnique'),
				'message' => __d('croogo', 'Email address already in use.'),
				'last' => true,
			)
		));

		$this->add('password', 'minlength', array(
			'rule' => array('minLength', 6),
			'message' => __d('croogo', 'Passwords must be at least 6 characters long.'),
		));

		$this->add('verify_password', 'valididentical', array(
			'rule' => array('validIdentical'),
		));

		$this->add('name', 'notempty',  array(
			'rule' => array('notEmpty'),
			'required' => true,
			'message' => __d('croogo', 'Name cannot be empty.'),
		));

		$this->add('name', 'validname',  array(
			'rule' => array('validName'),
			'required' => true,
			'message' => __d('croogo', 'This field must be alphanumeric.'),
		));

		$this->add('website', 'url', array(
			'rule' => array('url'),
			'message' => __d('croogo', 'This field must be a valid URL'),
			'allowEmpty' => true,
		));
	}

/**
 * validIdentical
 *
 * @param string $check
 * @return boolean
 */
	public function validIdentical($check) {
		$modelData = $this->getModel()->data;
		$modelAlias = $this->getModel()->alias;

		if (isset($modelData[$modelAlias]['password']) && isset($modelData[$modelAlias]['verify_password'])) {
			if ($modelData[$modelAlias]['password'] != $check['verify_password']) {
				return __d('users', 'Passwords do not match. Please, try again.');
			}
		}
		return false;
	}
}
