<?php

App::uses('CroogoTestFixture', 'Croogo.TestSuite');

class EmployeeFixture extends CroogoTestFixture {

/**
 * fields property
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'username' => array('type' => 'string', 'null' => false),
		'primary_department_id' => array('type' => 'integer', 'null' => true),
		'created' => 'datetime',
		'updated' => 'datetime'
	);

/**
 * records property
 *
 * @var array
 */
	public $records = array(
		array('username' => 'mark', 'created' => '2007-03-17 01:16:23', 'primary_department_id' => 2),
		array('username' => 'jack', 'created' => '2007-03-17 01:18:23', 'primary_department_id' => null),
		array('username' => 'larry', 'created' => '2007-03-17 01:20:23', 'primary_department_id' => null),
		array('username' => 'jose', 'created' => '2007-03-17 01:22:23', 'primary_department_id' => null),
	);
}
