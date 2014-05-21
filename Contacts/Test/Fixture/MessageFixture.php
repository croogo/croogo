<?php

namespace Croogo\Contacts\Test\Fixture;
class MessageFixture extends CroogoTestFixture {

	public $name = 'Message';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'contact_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100],
		'email' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100],
		'title' => ['type' => 'string', 'null' => false, 'default' => null],
		'body' => ['type' => 'text', 'null' => false, 'default' => null],
		'website' => ['type' => 'string', 'null' => false, 'default' => null],
		'phone' => ['type' => 'string', 'null' => false, 'default' => null],
		'address' => ['type' => 'text', 'null' => false, 'default' => null],
		'message_type' => ['type' => 'string', 'null' => true, 'default' => null],
		'status' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'PRIMARY' => ['type' => 'unique', 'columns' => 'id']],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
	);
}
