<?php

class MenuFixture extends CroogoTestFixture {

	public $name = 'Menu';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null),
		'alias' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'unique'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null),
		'status' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'weight' => array('type' => 'integer', 'null' => true, 'default' => null),
		'link_count' => array('type' => 'integer', 'null' => false, 'default' => null),
		'params' => array('type' => 'text', 'null' => true, 'default' => null),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'menu_alias' => array('column' => 'alias', 'unique' => 1),
			),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
		array(
			'id' => 3,
			'title' => 'Main Menu',
			'alias' => 'main',
			'description' => '',
			'status' => 1,
			'weight' => null,
			'link_count' => 4,
			'params' => '',
			'updated' => '2009-08-19 12:21:06',
			'created' => '2009-07-22 01:49:53'
		),
		array(
			'id' => 4,
			'title' => 'Footer',
			'alias' => 'footer',
			'description' => '',
			'status' => 1,
			'weight' => null,
			'link_count' => 2,
			'params' => '',
			'updated' => '2009-08-19 12:22:42',
			'created' => '2009-08-19 12:22:42'
		),
		array(
			'id' => 5,
			'title' => 'Meta',
			'alias' => 'meta',
			'description' => '',
			'status' => 1,
			'weight' => null,
			'link_count' => 4,
			'params' => '',
			'updated' => '2009-09-12 06:33:29',
			'created' => '2009-09-12 06:33:29'
		),
		array(
			'id' => 6,
			'title' => 'Blogroll',
			'alias' => 'blogroll',
			'description' => '',
			'status' => 1,
			'weight' => null,
			'link_count' => 2,
			'params' => '',
			'updated' => '2009-09-12 23:30:24',
			'created' => '2009-09-12 23:30:24'
		),
	);
}
