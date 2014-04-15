<?php
App::uses('CroogoAppModelValidator', 'Croogo.Model/Validator');

class CommentValidator extends CroogoAppModelValidator {
	public function __construct(Model $Model) {
		parent::__construct($Model);
		$this->add('body', 'notempty',  array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => __d('croogo', 'Body cannot be empty.'),
		));

		$this->add('name', 'notempty',  array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => __d('croogo', 'Name cannot be empty.'),
		));

		$this->add('email', 'email',  array(
			'rule' => 'email',
			'required' => true,
			'message' => __d('croogo', 'Email must be valid.'),
		));
	}
}
