<?php
App::uses('CroogoModelValidator', 'Croogo.Model/Validator');
class RoleValidator extends CroogoModelValidator {

	public function __construct(Model $Model) {
		parent::__construct($Model);

		$this->add('title', array(
			'notempty' =>  array(
				'rule' => array('notEmpty'),
				'required' => true,
				'message' => __d('croogo', 'Title cannot be empty.'),
			),
			'validname' =>  array(
				'rule' => array('validName'),
				'required' => true,
				'message' => __d('croogo', 'This field must be alphanumeric.'),
			)
		));

		$this->add('alias', array(
			'notempty' =>  array(
				'rule' => array('notEmpty'),
				'required' => true,
				'message' => __d('croogo', 'Alias cannot be empty.'),
			),
			'unique' => array(
				'rule' => array('isUnique'),
				'required' => true,
				'message' => __d('croogo', 'The alias has already been taken.'),
			),
			'validalias' =>  array(
				'rule' => array('validAlias'),
				'required' => true,
				'message' => __d('croogo', 'This field must be alphanumeric.'),
			)
		));
	}
}
