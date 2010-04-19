<?php

class AroFixture extends CakeTestFixture {
	public $name = 'Aro';
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'model' => array('type' => 'string', 'null' => true),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'alias' => array('type' => 'string', 'null' => true),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	public $records = array(
		array(
			'id' => '1',
			'parent_id' => '',
			'model' => 'Role',
			'foreign_key' => '1',
			'alias' => '',
			'lft' => '1',
			'rght' => '4',
		),
		array(
			'id' => '2',
			'parent_id' => '',
			'model' => 'Role',
			'foreign_key' => '2',
			'alias' => '',
			'lft' => '5',
			'rght' => '6',
		),
		array(
			'id' => '3',
			'parent_id' => '',
			'model' => 'Role',
			'foreign_key' => '3',
			'alias' => '',
			'lft' => '7',
			'rght' => '8',
		),
		array(
			'id' => '5',
			'parent_id' => '1',
			'model' => 'User',
			'foreign_key' => '1',
			'alias' => '',
			'lft' => '2',
			'rght' => '3',
		),
	);
}

?>