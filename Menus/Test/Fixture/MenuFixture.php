<?php

namespace Croogo\Menus\Test\Fixture;
class MenuFixture extends CroogoTestFixture {

	public $name = 'Menu';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
		'title' => ['type' => 'string', 'null' => false, 'default' => null],
		'alias' => ['type' => 'string', 'null' => false, 'default' => null],
		'description' => ['type' => 'text', 'null' => true, 'default' => null],
		'status' => ['type' => 'integer', 'length' => 1, 'null' => false, 'default' => '1'],
		'weight' => ['type' => 'integer', 'null' => true, 'default' => null],
		'link_count' => ['type' => 'integer', 'null' => false, 'default' => null],
		'params' => ['type' => 'text', 'null' => true, 'default' => null],
		'publish_start' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'publish_end' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'PRIMARY' => ['type' => 'unique', 'columns' => 'id'], 'menu_alias' => ['type' => 'unique', 'columns' => 'alias']],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
		array(
			'id' => 3,
			'title' => 'Main Menu',
			'alias' => 'main',
			'description' => '',
			'status' => 1,
			'weight' => null,
			'link_count' => 4,
			'params' => '',
			'updated' => '2009-08-19 12:21:06',
			'created' => '2009-07-22 01:49:53'
		),
		array(
			'id' => 4,
			'title' => 'Footer',
			'alias' => 'footer',
			'description' => '',
			'status' => 1,
			'weight' => null,
			'link_count' => 2,
			'params' => '',
			'updated' => '2009-08-19 12:22:42',
			'created' => '2009-08-19 12:22:42'
		),
		array(
			'id' => 5,
			'title' => 'Meta',
			'alias' => 'meta',
			'description' => '',
			'status' => 1,
			'weight' => null,
			'link_count' => 4,
			'params' => '',
			'updated' => '2009-09-12 06:33:29',
			'created' => '2009-09-12 06:33:29'
		),
		array(
			'id' => 6,
			'title' => 'Blogroll',
			'alias' => 'blogroll',
			'description' => '',
			'status' => 1,
			'weight' => null,
			'link_count' => 2,
			'params' => '',
			'updated' => '2009-09-12 23:30:24',
			'created' => '2009-09-12 23:30:24'
		),
	);
}
