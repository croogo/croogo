<?php

namespace Croogo\Users\Test\Fixture;
class RoleFixture extends CroogoTestFixture {

	public $name = 'Role';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'title' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100],
		'alias' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'updated' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'PRIMARY' => ['type' => 'unique', 'columns' => 'id'], 'role_alias' => ['type' => 'unique', 'columns' => 'alias']],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
		array(
			'id' => 1,
			'title' => 'Admin',
			'alias' => 'admin',
			'created' => '2009-04-05 00:10:34',
			'updated' => '2009-04-05 00:10:34'
		),
		array(
			'id' => 2,
			'title' => 'Registered',
			'alias' => 'registered',
			'created' => '2009-04-05 00:10:50',
			'updated' => '2009-04-06 05:20:38'
		),
		array(
			'id' => 3,
			'title' => 'Public',
			'alias' => 'public',
			'created' => '2009-04-05 00:12:38',
			'updated' => '2009-04-07 01:41:45'
		),
	);
}
