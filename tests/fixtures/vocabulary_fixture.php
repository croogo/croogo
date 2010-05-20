<?php
/* Vocabulary Fixture generated on: 2010-05-20 22:05:59 : 1274393819 */
class VocabularyFixture extends CakeTestFixture {
	var $name = 'Vocabulary';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'alias' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'unique'),
		'description' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'required' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'multiple' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'tags' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'plugin' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'weight' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'alias' => array('column' => 'alias', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'title' => 'Categories',
			'alias' => 'categories',
			'description' => '',
			'required' => 0,
			'multiple' => 1,
			'tags' => 0,
			'plugin' => '',
			'weight' => 1,
			'updated' => '2010-05-17 20:03:11',
			'created' => '2009-07-22 02:16:21'
		),
		array(
			'id' => 2,
			'title' => 'Tags',
			'alias' => 'tags',
			'description' => '',
			'required' => 0,
			'multiple' => 1,
			'tags' => 0,
			'plugin' => '',
			'weight' => 2,
			'updated' => '2010-05-17 20:03:11',
			'created' => '2009-07-22 02:16:34'
		),
	);
}
?>