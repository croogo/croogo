<?php

class NodeFixture extends CroogoTestFixture {

	public $name = 'Node';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 20),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20),
		'title' => array('type' => 'string', 'null' => false, 'default' => null),
		'slug' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'unique'),
		'body' => array('type' => 'text', 'null' => false, 'default' => null, 'key' => 'index'),
		'excerpt' => array('type' => 'text', 'null' => true, 'default' => null),
		'status' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'mime_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'comment_status' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 1),
		'comment_count' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'promote' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'path' => array('type' => 'string', 'null' => false, 'default' => null),
		'terms' => array('type' => 'text', 'null' => true, 'default' => null),
		'sticky' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => null),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => null),
		'visibility_roles' => array('type' => 'text', 'null' => true, 'default' => null),
		'type' => array('type' => 'string', 'null' => false, 'default' => 'node', 'length' => 100),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'slug' => array('column' => 'slug', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
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
	);
}
