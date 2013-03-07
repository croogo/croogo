<?php

class VocabularyFixture extends CroogoTestFixture {

	public $name = 'Vocabulary';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null),
		'alias' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'unique'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null),
		'required' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'multiple' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'tags' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'plugin' => array('type' => 'string', 'null' => true, 'default' => null),
		'weight' => array('type' => 'integer', 'null' => true, 'default' => null),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'vocabulary_alias' => array('column' => 'alias', 'unique' => 1),
			),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
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
