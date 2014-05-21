<?php

namespace Croogo\Contacts\Test\Fixture;
class ContactFixture extends CroogoTestFixture {

	public $name = 'Contact';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'title' => ['type' => 'string', 'null' => false, 'default' => null],
		'alias' => ['type' => 'string', 'null' => false, 'default' => null],
		'body' => ['type' => 'text', 'null' => true, 'default' => null],
		'name' => ['type' => 'string', 'null' => true, 'default' => null],
		'position' => ['type' => 'string', 'null' => true, 'default' => null],
		'address' => ['type' => 'text', 'null' => true, 'default' => null],
		'address2' => ['type' => 'text', 'null' => true, 'default' => null],
		'state' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100],
		'country' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100],
		'postcode' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100],
		'phone' => ['type' => 'string', 'null' => true, 'default' => null],
		'fax' => ['type' => 'string', 'null' => true, 'default' => null],
		'email' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100],
		'message_status' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
		'message_archive' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
		'message_count' => ['type' => 'integer', 'null' => false, 'default' => '0'],
		'message_spam_protection' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'message_captcha' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'message_notify' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
		'status' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
		'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'PRIMARY' => ['type' => 'unique', 'columns' => 'id']],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
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
