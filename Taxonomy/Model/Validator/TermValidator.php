<?php
App::uses('CroogoModelValidator', 'Croogo.Model/Validator');
class TermValidator extends CroogoModelValidator {
	public function __construct(Model $Model) {
		parent::__construct($Model);

		$this->add('slug', 'notempty',  array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => __d('croogo', 'Slug cannot be empty.'),
		));

		$this->add('slug', 'unique',  array(
			'rule' => 'isUnique',
			'required' => true,
			'message' => __d('croogo', 'This slug has already been taken.'),
		));
	}
}
