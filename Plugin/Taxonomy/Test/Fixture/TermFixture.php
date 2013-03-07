<?php

class TermFixture extends CroogoTestFixture {

	public $name = 'Term';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null),
		'slug' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'unique'),
		'description' => array('type' => 'text', 'null' => false, 'default' => null),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'term_slug' => array('column' => 'slug', 'unique' => 1),
			),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
		array(
			'id' => 1,
			'title' => 'Uncategorized',
			'slug' => 'uncategorized',
			'description' => '',
			'updated' => '2009-07-22 03:38:43',
			'created' => '2009-07-22 03:34:56'
		),
		array(
			'id' => 2,
			'title' => 'Announcements',
			'slug' => 'announcements',
			'description' => '',
			'updated' => '2010-05-16 23:57:06',
			'created' => '2009-07-22 03:45:37'
		),
		array(
			'id' => 3,
			'title' => 'mytag',
			'slug' => 'mytag',
			'description' => '',
			'updated' => '2009-08-26 14:42:43',
			'created' => '2009-08-26 14:42:43'
		),
	);
}
