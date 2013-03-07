<?php

class CommentFixture extends CroogoTestFixture {

	public $name = 'Comment';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 20),
		'node_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50),
		'email' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100),
		'website' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200),
		'ip' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100),
		'title' => array('type' => 'string', 'null' => true, 'default' => null),
		'body' => array('type' => 'text', 'null' => false, 'default' => null),
		'rating' => array('type' => 'integer', 'null' => true, 'default' => null),
		'status' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'notify' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'type' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100),
		'comment_type' => array('type' => 'string', 'null' => false, 'default' => 'comment', 'length' => 100),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => null),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => null),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
		array(
			'id' => 1,
			'parent_id' => null,
			'node_id' => 1,
			'user_id' => 0,
			'name' => 'Mr Croogo',
			'email' => 'email@example.com',
			'website' => 'http://www.croogo.org',
			'ip' => '127.0.0.1',
			'title' => '',
			'body' => 'Hi, this is the first comment.',
			'rating' => null,
			'status' => 1,
			'notify' => 0,
			'type' => 'blog',
			'comment_type' => 'comment',
			'lft' => 1,
			'rght' => 2,
			'updated' => '2009-12-25 12:00:00',
			'created' => '2009-12-25 12:00:00'
		),
		array(
			'id' => 2,
			'parent_id' => null,
			'node_id' => 1,
			'user_id' => 0,
			'name' => 'Mrs Croogo',
			'email' => 'email@example.com',
			'website' => 'http://www.croogo.org',
			'ip' => '127.0.0.1',
			'title' => '',
			'body' => 'Hi, this is the second comment.',
			'rating' => null,
			'status' => 0,
			'notify' => 0,
			'type' => 'blog',
			'comment_type' => 'comment',
			'lft' => 3,
			'rght' => 4,
			'updated' => '2009-12-25 12:00:00',
			'created' => '2009-12-25 12:00:00'
		),
	);
}
