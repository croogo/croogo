<?php
App::uses('CroogoModelValidator', 'Croogo.Model/Validator');
class BlocksAppValidator extends CroogoModelValidator {

	public function __construct(Model $Model) {
		parent::__construct($Model);
		$this->add('title', 'notempty',  array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => __d('croogo', 'Title cannot be empty.'),
		));

		$this->add('alias', 'unique',  array(
			'rule' => 'isUnique',
			'message' => __d('croogo', 'This alias has already been taken'),
		));

		$this->add('alias', 'notempty',  array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => __d('croogo', 'Alias cannot be empty.'),
		));
	}
}
