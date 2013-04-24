<?php

App::uses('CroogoTestFixture', 'Croogo.TestSuite');

class MembershipFixture extends CroogoTestFixture {

/**
 * fields property
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'employee_id' => array('type' => 'integer', 'null' => false),
		'department_id' => array('type' => 'integer', 'null' => false),
	);

/**
 * records property
 *
 * @var array
 */
	public $records = array(
		array('employee_id' => 1, 'department_id' => 1),
		array('employee_id' => 1, 'department_id' => 4),
		array('employee_id' => 4, 'department_id' => 3),
	);
}
