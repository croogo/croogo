<?php
/* Taxonomy Fixture generated on: 2010-05-20 22:05:50 : 1274393810 */
class TaxonomyFixture extends CakeTestFixture {
	var $name = 'Taxonomy';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 20, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 20),
		'term_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'vocabulary_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'parent_id' => NULL,
			'term_id' => 1,
			'vocabulary_id' => 1,
			'lft' => 1,
			'rght' => 2
		),
		array(
			'id' => 2,
			'parent_id' => NULL,
			'term_id' => 2,
			'vocabulary_id' => 1,
			'lft' => 3,
			'rght' => 4
		),
		array(
			'id' => 3,
			'parent_id' => NULL,
			'term_id' => 3,
			'vocabulary_id' => 2,
			'lft' => 1,
			'rght' => 2
		),
	);
}
?>