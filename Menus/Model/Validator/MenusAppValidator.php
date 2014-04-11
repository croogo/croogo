<?php
App::uses('CroogoModelValidator', 'Croogo.Model/Validator');
class MenusAppValidator extends CroogoModelValidator {

	public function __construct(Model $Model) {
		parent::__construct($Model);
		$this->add('title', 'notempty',  array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => __d('croogo', 'Title cannot be empty.'),
		));

	}
}
