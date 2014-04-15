<?php
App::uses('CroogoAppModelValidator', 'Croogo.Model/Validator');
class ContactsAppValidator extends CroogoAppModelValidator {

	public function __construct(Model $Model) {
		parent::__construct($Model);
		$this->add('title', 'notempty',  array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => __d('croogo', 'Title cannot be empty.'),
		));

		$this->add('email', 'email',  array(
			'rule' => 'email',
			'required' => true,
			'message' => __d('croogo', 'Please provide a valid email address'),
		));
	}
}
