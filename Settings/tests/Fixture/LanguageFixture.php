<?php

namespace Croogo\Settings\Test\Fixture;
class LanguageFixture extends CroogoTestFixture {

	public $name = 'Language';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'title' => ['type' => 'string', 'null' => false, 'default' => null],
		'native' => ['type' => 'string', 'null' => true, 'default' => null],
		'alias' => ['type' => 'string', 'null' => false, 'default' => null],
		'status' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
		'weight' => ['type' => 'integer', 'null' => true, 'default' => null],
		'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'PRIMARY' => ['type' => 'unique', 'columns' => 'id']],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
		array(
			'id' => 1,
			'title' => 'English',
			'native' => 'English',
			'alias' => 'eng',
			'status' => 1,
			'weight' => 1,
			'updated' => '2009-11-02 21:37:38',
			'created' => '2009-11-02 20:52:00'
		),
	);
}
