<?php
/* Term Fixture generated on: 2010-05-20 22:05:52 : 1274393812 */
class TermFixture extends CakeTestFixture {
	var $name = 'Term';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'slug' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'unique'),
		'description' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'slug' => array('column' => 'slug', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
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
?>