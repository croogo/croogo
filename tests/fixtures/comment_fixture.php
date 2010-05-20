<?php
/* Comment Fixture generated on: 2010-05-20 22:05:32 : 1274393792 */
class CommentFixture extends CakeTestFixture {
	var $name = 'Comment';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 20, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 20),
		'node_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 20),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 50),
		'email' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'website' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 200),
		'ip' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'body' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'rating' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'status' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'notify' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'type' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'comment_type' => array('type' => 'string', 'null' => false, 'default' => 'comment', 'length' => 100),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'parent_id' => NULL,
			'node_id' => 1,
			'user_id' => 0,
			'name' => 'Mr Croogo',
			'email' => 'email@example.com',
			'website' => 'http://www.croogo.org',
			'ip' => '127.0.0.1',
			'title' => '',
			'body' => 'Hi, this is the first comment.',
			'rating' => NULL,
			'status' => 1,
			'notify' => 0,
			'type' => 'blog',
			'comment_type' => 'comment',
			'lft' => 1,
			'rght' => 2,
			'updated' => '2009-12-25 12:00:00',
			'created' => '2009-12-25 12:00:00'
		),
	);
}
?>