<?php

class LanguageFixture extends CroogoTestFixture {

	public $name = 'Language';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null),
		'native' => array('type' => 'string', 'null' => true, 'default' => null),
		'alias' => array('type' => 'string', 'null' => false, 'default' => null),
		'status' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'weight' => array('type' => 'integer', 'null' => true, 'default' => null),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
		array(
			'id' => 1,
			'title' => 'English',
			'native' => 'English',
			'alias' => 'eng',
			'status' => 1,
			'weight' => 1,
			'updated' => '2009-11-02 21:37:38',
			'created' => '2009-11-02 20:52:00'
		),
	);
}
