<?php

class MessageFixture extends CroogoTestFixture {

	public $name = 'Message';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'contact_id' => array('type' => 'integer', 'null' => false, 'default' => null),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100),
		'email' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100),
		'title' => array('type' => 'string', 'null' => false, 'default' => null),
		'body' => array('type' => 'text', 'null' => false, 'default' => null),
		'website' => array('type' => 'string', 'null' => false, 'default' => null),
		'phone' => array('type' => 'string', 'null' => false, 'default' => null),
		'address' => array('type' => 'text', 'null' => false, 'default' => null),
		'message_type' => array('type' => 'string', 'null' => true, 'default' => null),
		'status' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
	);
}
