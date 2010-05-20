<?php
/* Contact Fixture generated on: 2010-05-20 22:05:35 : 1274393795 */
class ContactFixture extends CakeTestFixture {
	var $name = 'Contact';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'alias' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'body' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'position' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'address' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'address2' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'state' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'country' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'postcode' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'phone' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'fax' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'email' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'message_status' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'message_archive' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'message_count' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'message_spam_protection' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'message_captcha' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'message_notify' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'status' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
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
?>