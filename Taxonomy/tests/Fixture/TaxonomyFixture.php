<?php

namespace Croogo\Taxonomy\Test\Fixture;
class TaxonomyFixture extends CroogoTestFixture {

	public $name = 'Taxonomy';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20],
		'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 20],
		'term_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
		'vocabulary_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
		'lft' => ['type' => 'integer', 'null' => true, 'default' => null],
		'rght' => ['type' => 'integer', 'null' => true, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'PRIMARY' => ['type' => 'unique', 'columns' => 'id']],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
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
