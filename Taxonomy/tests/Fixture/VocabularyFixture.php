<?php

namespace Croogo\Taxonomy\Test\Fixture;
class VocabularyFixture extends CroogoTestFixture {

	public $name = 'Vocabulary';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
		'title' => ['type' => 'string', 'null' => false, 'default' => null],
		'alias' => ['type' => 'string', 'null' => false, 'default' => null],
		'description' => ['type' => 'text', 'null' => true, 'default' => null],
		'required' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'multiple' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'tags' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'plugin' => ['type' => 'string', 'null' => true, 'default' => null],
		'weight' => ['type' => 'integer', 'null' => true, 'default' => null],
		'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'PRIMARY' => ['type' => 'unique', 'columns' => 'id'], 'vocabulary_alias' => ['type' => 'unique', 'columns' => 'alias']],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
		array(
			'id' => 1,
			'title' => 'Categories',
			'alias' => 'categories',
			'description' => '',
			'required' => 1,
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
