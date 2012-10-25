<?php

class MetaFixture extends CroogoTestFixture {

	public $name = 'Meta';

	public $table = 'meta';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => false, 'default' => 'Node'),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 20),
		'key' => array('type' => 'string', 'null' => false, 'default' => null),
		'value' => array('type' => 'text', 'null' => true, 'default' => null),
		'weight' => array('type' => 'integer', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
		array(
			'id' => 1,
			'model' => 'Node',
			'foreign_key' => 1,
			'key' => 'meta_keywords',
			'value' => 'key1, key2',
			'weight' => null
		),
	);
}
