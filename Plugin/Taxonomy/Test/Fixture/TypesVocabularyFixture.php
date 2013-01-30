<?php

class TypesVocabularyFixture extends CroogoTestFixture {

	public $name = 'TypesVocabulary';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'type_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
		'vocabulary_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
		'weight' => array('type' => 'integer', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
		array(
			'id' => 31,
			'type_id' => 2,
			'vocabulary_id' => 2,
			'weight' => null
		),
		array(
			'id' => 30,
			'type_id' => 2,
			'vocabulary_id' => 1,
			'weight' => null
		),
		array(
			'id' => 25,
			'type_id' => 4,
			'vocabulary_id' => 2,
			'weight' => null
		),
		array(
			'id' => 24,
			'type_id' => 4,
			'vocabulary_id' => 1,
			'weight' => null
		),
	);
}
