<?php

class ContactFixture extends CroogoTestFixture {

	public $name = 'Contact';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null),
		'alias' => array('type' => 'string', 'null' => false, 'default' => null),
		'body' => array('type' => 'text', 'null' => true, 'default' => null),
		'name' => array('type' => 'string', 'null' => true, 'default' => null),
		'position' => array('type' => 'string', 'null' => true, 'default' => null),
		'address' => array('type' => 'text', 'null' => true, 'default' => null),
		'address2' => array('type' => 'text', 'null' => true, 'default' => null),
		'state' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'country' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'postcode' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'phone' => array('type' => 'string', 'null' => true, 'default' => null),
		'fax' => array('type' => 'string', 'null' => true, 'default' => null),
		'email' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'message_status' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'message_archive' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'message_count' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'message_spam_protection' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'message_captcha' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'message_notify' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'status' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
		array(
			'id' => 1,
			'title' => 'Contact',
			'alias' => 'contact',
			'body' => '',
			'name' => '',
			'position' => '',
			'address' => '',
			'address2' => '',
			'state' => '',
			'country' => '',
			'postcode' => '',
			'phone' => '',
			'fax' => '',
			'email' => 'you@your-site.com',
			'message_status' => 1,
			'message_archive' => 0,
			'message_count' => 0,
			'message_spam_protection' => 0,
			'message_captcha' => 0,
			'message_notify' => 1,
			'status' => 1,
			'updated' => '2009-10-07 22:07:49',
			'created' => '2009-09-16 01:45:17'
		),
	);
}
