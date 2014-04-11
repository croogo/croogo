<?php
class MessageValidator extends ContactsAppValidator {
	public function __construct(Model $Model) {
		parent::__construct($Model);

		$this->add('name', 'notempty',  array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => __d('croogo', 'Name cannot be empty.'),
		));

		$this->add('body', 'notempty',  array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => __d('croogo', 'Body cannot be empty.'),
		));
	}
}
