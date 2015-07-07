<?php

namespace Croogo\Nodes\Test\Fixture;
use Croogo\Core\TestSuite\CroogoTestFixture;

class NodeFixture extends CroogoTestFixture {

	public $name = 'Node';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 20],
		'user_id' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20],
		'title' => ['type' => 'string', 'null' => false, 'default' => null],
		'slug' => ['type' => 'string', 'null' => false, 'default' => null],
		'body' => ['type' => 'text', 'null' => false, 'default' => null],
		'excerpt' => ['type' => 'text', 'null' => true, 'default' => null],
		'status' => ['type' => 'integer', 'length' => 1, 'null' => false, 'default' => '0'],
		'mime_type' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100],
		'comment_status' => ['type' => 'integer', 'null' => false, 'default' => '1', 'length' => 1],
		'comment_count' => ['type' => 'integer', 'null' => true, 'default' => '0'],
		'promote' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'path' => ['type' => 'string', 'null' => false, 'default' => null],
		'terms' => ['type' => 'text', 'null' => true, 'default' => null],
		'sticky' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'lft' => ['type' => 'integer', 'null' => true, 'default' => null],
		'rght' => ['type' => 'integer', 'null' => true, 'default' => null],
		'visibility_roles' => ['type' => 'text', 'null' => true, 'default' => null],
		'type' => ['type' => 'string', 'null' => false, 'default' => 'node', 'length' => 100],
		'publish_start' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'publish_end' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'_constraints' => [
			'primary' => ['type' => 'primary', 'columns' => ['id']],
			'slug' => ['type' => 'unique', 'columns' => 'slug']
		],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
		array(
			'id' => 1,
			'parent_id' => null,
			'user_id' => 1,
			'title' => 'Hello World',
			'slug' => 'hello-world',
			'body' => '<p>Welcome to Croogo. This is your first post. You can edit or delete it from the admin panel.</p>',
			'excerpt' => '',
			'status' => 1,
			'mime_type' => '',
			'comment_status' => 2,
			'comment_count' => 1,
			'promote' => 1,
			'path' => '/blog/hello-world',
			'terms' => '{\"1\":\"uncategorized\"}',
			'sticky' => 0,
			'lft' => 1,
			'rght' => 2,
			'visibility_roles' => '',
			'type' => 'blog',
			'updated' => '2009-12-25 11:00:00',
			'created' => '2009-12-25 11:00:00'
		),
		array(
			'id' => 2,
			'parent_id' => null,
			'user_id' => 1,
			'title' => 'About',
			'slug' => 'about',
			'body' => '<p>This is an example of a Croogo page, you could edit this to put information about yourself or your site.</p>',
			'excerpt' => '',
			'status' => 1,
			'mime_type' => '',
			'comment_status' => 0,
			'comment_count' => 0,
			'promote' => 0,
			'path' => '/about',
			'terms' => '',
			'sticky' => 0,
			'lft' => 1,
			'rght' => 2,
			'visibility_roles' => '',
			'type' => 'page',
			'updated' => '2009-12-25 22:00:00',
			'created' => '2009-12-25 22:00:00'
		),
		array(
			'id' => 3,
			'parent_id' => null,
			'user_id' => 1,
			'title' => 'Protected',
			'slug' => 'protected',
			'body' => '<p>This page is only visible to admin</p>',
			'excerpt' => '',
			'status' => 1,
			'mime_type' => '',
			'comment_status' => 0,
			'comment_count' => 0,
			'promote' => 1,
			'path' => '/page/protected',
			'terms' => '',
			'sticky' => 0,
			'lft' => 3,
			'rght' => 4,
			'visibility_roles' => '["1"]',
			'type' => 'page',
			'updated' => '2013-09-12 20:00:00',
			'created' => '2013-09-12 20:00:00'
		),
	);
}
