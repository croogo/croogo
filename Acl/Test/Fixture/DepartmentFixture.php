<?php

namespace Croogo\Acl\Test\Fixture;

use Croogo\TestSuite\CroogoTestFixture;
class DepartmentFixture extends CroogoTestFixture {

/**
 * fields property
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false),
	);

/**
 * records property
 *
 * @var array
 */
	public $records = array(
		array('name' => 'Development'),
		array('name' => 'Design'),
		array('name' => 'Management'),
		array('name' => 'Sales'),
		array('name' => 'Support')
	);
}
