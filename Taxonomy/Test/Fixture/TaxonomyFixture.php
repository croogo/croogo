<?php

class TaxonomyFixture extends CroogoTestFixture {

	public $name = 'Taxonomy';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 20),
		'term_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
		'vocabulary_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => null),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
		array(
			'id' => 1,
			'parent_id' => null,
			'term_id' => 1,
			'vocabulary_id' => 1,
			'lft' => 1,
			'rght' => 2
		),
		array(
			'id' => 2,
			'parent_id' => null,
			'term_id' => 2,
			'vocabulary_id' => 1,
			'lft' => 3,
			'rght' => 4
		),
		array(
			'id' => 3,
			'parent_id' => null,
			'term_id' => 3,
			'vocabulary_id' => 2,
			'lft' => 1,
			'rght' => 2
		),
	);
}
