<?php
App::uses('CroogoAppModelValidator', 'Croogo.Model/Validator');
class SettingValidator extends CroogoAppModelValidator {

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
