<?php
class BlockValidator extends BlocksAppValidator {

	public function __construct(Model $Model) {
		parent::__construct($Model);

		$this->add('body', 'notempty',  array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => __d('croogo', 'Body cannot be empty.'),
		));
	}
}
