<?php

App::uses('CroogoTestFixture', 'Croogo.TestSuite');

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
