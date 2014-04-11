<?php
App::uses('MenusAppValidator', 'Menus.Model/Validator');
class LinkValidator extends MenusAppValidator {
	public function __construct(Model $Model) {
		parent::__construct($Model);

		$this->add('link', 'notempty',  array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => __d('croogo', 'Link cannot be empty.'),
		));
	}
}
