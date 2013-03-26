<?php

class CommentsSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $comments = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 20),
		'node_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'email' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'website' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'ip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'rating' => array('type' => 'integer', 'null' => true, 'default' => null),
		'status' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'notify' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'type' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'comment_type' => array('type' => 'string', 'null' => false, 'default' => 'comment', 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => null),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => null),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

}
