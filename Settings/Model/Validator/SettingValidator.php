<?php
App::uses('CroogoModelValidator', 'Croogo.Model/Validator');
class SettingValidator extends CroogoModelValidator {

	public function __construct(Model $Model) {
		parent::__construct($Model);

		$this->add('key', 'notempty',  array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => __d('croogo', 'Key cannot be empty.'),
		));

		$this->add('key', 'unique',  array(
			'rule' => 'isUnique',
			'required' => true,
			'message' => __d('croogo', 'This key has already been taken.'),
		));
	}
}
