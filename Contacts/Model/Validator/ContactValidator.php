<?php
class ContactValidator extends ContactsAppValidator {

	public function __construct(Model $Model) {
		parent::__construct($Model);

		$this->add('alias', 'notempty',  array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => __d('croogo', 'Alias cannot be empty.'),
		));

		$this->add('alias', 'unique',  array(
			'rule' => 'isUnique',
			'required' => true,
			'message' => __d('croogo', 'This alias has already been taken.'),
		));
	}
}
