<?php

namespace Croogo\Acl\Test\Fixture;

use Croogo\TestSuite\CroogoTestFixture;
class EmployeeFixture extends CroogoTestFixture {

/**
 * fields property
 *
 * @var array
 */
	public $fields = array(
		'id' => ['type' => 'integer'],
		'username' => ['type' => 'string', 'null' => false],
		'primary_department_id' => ['type' => 'integer', 'null' => true],
		'created' => 'datetime',
		'updated' => 'datetime',
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
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
