<?php

/**
 * InstallRoleFixture
 *
 */
namespace Croogo\Install\Test\Fixture;
class InstallRoleFixture extends CakeTestFixture {

/**
 * Table name
 */
	public $table = 'roles';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'title' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
		'alias' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'updated' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'PRIMARY' => ['type' => 'unique', 'columns' => 'id'], 'role_alias' => ['type' => 'unique', 'columns' => 'alias']],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'title' => 'Admin',
			'alias' => 'admin',
			'created' => '2009-04-05 00:10:34',
			'updated' => '2009-04-05 00:10:34'
		),
		array(
			'id' => '2',
			'title' => 'Registered',
			'alias' => 'registered',
			'created' => '2009-04-05 00:10:50',
			'updated' => '2009-04-06 05:20:38'
		),
		array(
			'id' => '3',
			'title' => 'Public',
			'alias' => 'public',
			'created' => '2009-04-05 00:12:38',
			'updated' => '2009-04-07 01:41:45'
		),
	);

}
