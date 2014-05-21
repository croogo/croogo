<?php

namespace Croogo\Users\Test\Fixture;
class UserFixture extends CroogoTestFixture {

	public $name = 'User';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20],
		'role_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'username' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 60],
		'password' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50],
		'email' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100],
		'website' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100],
		'activation_key' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 60],
		'image' => ['type' => 'string', 'null' => true, 'default' => null],
		'bio' => ['type' => 'text', 'null' => true, 'default' => null],
		'timezone' => ['type' => 'string', 'null' => true, 'default' => '0', 'length' => 10],
		'status' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'updated_by' => ['type' => 'integer', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'created_by' => ['type' => 'integer', 'null' => true, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'PRIMARY' => ['type' => 'unique', 'columns' => 'id']],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
		array(
			'id' => 1,
			'role_id' => 1,
			'username' => 'admin',
			'password' => 'c054b152596745efa1d197b809fa7fc70ce586e5',
			'name' => 'Administrator',
			'email' => 'you@your-site.com',
			'website' => '/about',
			'activation_key' => '',
			'image' => '',
			'bio' => '',
			'timezone' => '0',
			'status' => 1,
			'updated' => '2009-10-07 22:23:27',
			'created' => '2009-04-05 00:20:34'
		),
		array(
			'id' => 2,
			'role_id' => 1,
			'username' => 'rchavik',
			'password' => 'ab4d1d3ab4d1d3ab4d1d3ab4d1d3aaaaab4d1d3a',
			'name' => 'Rachman Chavik',
			'email' => 'me@your-site.com',
			'website' => '/about',
			'activation_key' => '',
			'image' => '',
			'bio' => '',
			'timezone' => '0',
			'status' => 1,
			'updated' => '2010-01-07 22:23:27',
			'created' => '2010-01-05 00:00:00'
		),
		array(
			'id' => 3,
			'role_id' => 3,
			'username' => 'yvonne',
			'password' => 'ec84aaa5d1a656a1b4d78cf9ad9fdfe3',
			'name' => 'Yvonne',
			'email' => 'yvonne@your-site.com',
			'website' => '/about',
			'activation_key' => '92e35177eba73c6524d4561d3047c0c2',
			'image' => '',
			'bio' => '',
			'timezone' => '0',
			'status' => 1,
			'updated' => '2011-04-25 18:50:27',
			'created' => '2011-04-25 18:50:27'
		),
	);
}
