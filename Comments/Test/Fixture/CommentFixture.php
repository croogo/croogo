<?php

namespace Croogo\Comments\Test\Fixture;
class CommentFixture extends CroogoTestFixture {

	public $name = 'Comment';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20],
		'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 20],
		'user_id' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20],
		'model' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50],
		'foreign_key' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50],
		'email' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100],
		'website' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 200],
		'ip' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100],
		'title' => ['type' => 'string', 'null' => true, 'default' => null],
		'body' => ['type' => 'text', 'null' => false, 'default' => null],
		'rating' => ['type' => 'integer', 'null' => true, 'default' => null],
		'status' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'notify' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'type' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100],
		'comment_type' => ['type' => 'string', 'null' => false, 'default' => 'comment', 'length' => 100],
		'lft' => ['type' => 'integer', 'null' => true, 'default' => null],
		'rght' => ['type' => 'integer', 'null' => true, 'default' => null],
		'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'PRIMARY' => ['type' => 'unique', 'columns' => 'id']],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
		array(
			'id' => 1,
			'parent_id' => null,
			'model' => 'Node',
			'foreign_key' => 1,
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
			'model' => 'Node',
			'foreign_key' => 1,
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
