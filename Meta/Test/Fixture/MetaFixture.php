<?php

namespace Croogo\Meta\Test\Fixture;
class MetaFixture extends CroogoTestFixture {

	public $name = 'Meta';

	public $table = 'meta';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20],
		'model' => ['type' => 'string', 'null' => false, 'default' => 'Node'],
		'foreign_key' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 20],
		'key' => ['type' => 'string', 'null' => false, 'default' => null],
		'value' => ['type' => 'text', 'null' => true, 'default' => null],
		'weight' => ['type' => 'integer', 'null' => true, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'PRIMARY' => ['type' => 'unique', 'columns' => 'id']],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
		array(
			'id' => 1,
			'model' => 'Node',
			'foreign_key' => 1,
			'key' => 'meta_keywords',
			'value' => 'key1, key2',
			'weight' => null
		),
	);
}
