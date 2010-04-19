<?php
class TypesVocabularyFixture extends CakeTestFixture {
	public $name = 'TypesVocabulary';
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'type_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'vocabulary_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'weight' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	public $records = array(
		array(
			'id' => '23',
			'type_id' => '2',
			'vocabulary_id' => '2',
			'weight' => '',
		),
		array(
			'id' => '22',
			'type_id' => '2',
			'vocabulary_id' => '1',
			'weight' => '',
		),
		array(
			'id' => '25',
			'type_id' => '4',
			'vocabulary_id' => '2',
			'weight' => '',
		),
		array(
			'id' => '24',
			'type_id' => '4',
			'vocabulary_id' => '1',
			'weight' => '',
		),
	);
}

?>