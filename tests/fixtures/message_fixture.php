<?php
/* Message Fixture generated on: 2010-05-20 22:05:43 : 1274393803 */
class MessageFixture extends CakeTestFixture {
	var $name = 'Message';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'contact_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'email' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'body' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'website' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'phone' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'address' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'message_type' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'status' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
	);
}
?>