<?php
/* Menu Fixture generated on: 2010-05-20 22:05:41 : 1274393801 */
class MenuFixture extends CakeTestFixture {
	var $name = 'Menu';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'alias' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'unique'),
		'description' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'status' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'weight' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'link_count' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'params' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'alias' => array('column' => 'alias', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 3,
			'title' => 'Main Menu',
			'alias' => 'main',
			'description' => '',
			'status' => 1,
			'weight' => NULL,
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
			'weight' => NULL,
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
			'weight' => NULL,
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
			'weight' => NULL,
			'link_count' => 2,
			'params' => '',
			'updated' => '2009-09-12 23:30:24',
			'created' => '2009-09-12 23:30:24'
		),
	);
}
?>