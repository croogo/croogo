<?php

namespace Croogo\Croogo\Test\Fixture;

use Croogo\TestSuite\CroogoTestFixture;
class TrackableFixture extends CroogoTestFixture {

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20],
		'title' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 60],
		'created_by' => ['type' => 'integer', 'null' => false, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'updated_by' => ['type' => 'integer', 'null' => false, 'default' => null],
		'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'PRIMARY' => ['type' => 'unique', 'columns' => 'id']],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
	);

}
