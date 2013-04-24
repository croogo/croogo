<?php

class AroFixture extends CroogoTestFixture {

	public $name = 'Aro';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'model' => array('type' => 'string', 'null' => true),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'alias' => array('type' => 'string', 'null' => true),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
		array(
			'id' => 1,
			'parent_id' => 2,
			'model' => 'Role',
			'foreign_key' => 1,
			'alias' => 'Role-admin',
			'lft' => 3,
			'rght' => 8,
		),
		array(
			'id' => 2,
			'parent_id' => 3,
			'model' => 'Role',
			'foreign_key' => 2,
			'alias' => 'Role-registered',
			'lft' => 2,
			'rght' => 11,
		),
		array(
			'id' => 3,
			'parent_id' => null,
			'model' => 'Role',
			'foreign_key' => 3,
			'alias' => 'Role-public',
			'lft' => 1,
			'rght' => 12,
		),
		array(
			'id' => 4,
			'parent_id' => 1,
			'model' => 'User',
			'foreign_key' => 1,
			'alias' => 'admin',
			'lft' => 4,
			'rght' => 5
		),
		array(
			'id' => 5,
			'parent_id' => 1,
			'model' => 'User',
			'foreign_key' => 2,
			'alias' => 'rchavik',
			'lft' => 6,
			'rght' => 7,
		),
		array(
			'id' => 6,
			'parent_id' => 3,
			'model' => 'User',
			'foreign_key' => 3,
			'alias' => 'yvonne',
			'lft' => 9,
			'rght' => 10,
		),
	);
}
