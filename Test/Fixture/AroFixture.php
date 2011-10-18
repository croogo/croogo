<?php
/* Aro Fixture generated on: 2010-05-20 22:05:26 : 1274393786 */
class AroFixture extends CakeTestFixture {
	var $name = 'Aro';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'model' => array('type' => 'string', 'null' => true),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'alias' => array('type' => 'string', 'null' => true),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'parent_id' => NULL,
			'model' => 'Role',
			'foreign_key' => 1,
			'alias' => '',
			'lft' => 1,
			'rght' => 4
		),
		array(
			'id' => 2,
			'parent_id' => NULL,
			'model' => 'Role',
			'foreign_key' => 2,
			'alias' => '',
			'lft' => 5,
			'rght' => 6
		),
		array(
			'id' => 3,
			'parent_id' => NULL,
			'model' => 'Role',
			'foreign_key' => 3,
			'alias' => '',
			'lft' => 7,
			'rght' => 8
		),
		array(
			'id' => 5,
			'parent_id' => 1,
			'model' => 'User',
			'foreign_key' => 1,
			'alias' => '',
			'lft' => 2,
			'rght' => 3
		),
	);
}
?>