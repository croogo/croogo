<?php

class RegionFixture extends CroogoTestFixture {

	public $name = 'Region';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100),
		'alias' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'key' => 'unique'),
		'description' => array('type' => 'text', 'null' => false, 'default' => null),
		'block_count' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'region_alias' => array('column' => 'alias', 'unique' => 1),
			),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
		array(
			'id' => 3,
			'title' => 'none',
			'alias' => '',
			'description' => '',
			'block_count' => 0
		),
		array(
			'id' => 4,
			'title' => 'right',
			'alias' => 'right',
			'description' => '',
			'block_count' => 6
		),
		array(
			'id' => 6,
			'title' => 'left',
			'alias' => 'left',
			'description' => '',
			'block_count' => 0
		),
		array(
			'id' => 7,
			'title' => 'header',
			'alias' => 'header',
			'description' => '',
			'block_count' => 0
		),
		array(
			'id' => 8,
			'title' => 'footer',
			'alias' => 'footer',
			'description' => '',
			'block_count' => 0
		),
		array(
			'id' => 9,
			'title' => 'region1',
			'alias' => 'region1',
			'description' => '',
			'block_count' => 0
		),
		array(
			'id' => 10,
			'title' => 'region2',
			'alias' => 'region2',
			'description' => '',
			'block_count' => 0
		),
		array(
			'id' => 11,
			'title' => 'region3',
			'alias' => 'region3',
			'description' => '',
			'block_count' => 0
		),
		array(
			'id' => 12,
			'title' => 'region4',
			'alias' => 'region4',
			'description' => '',
			'block_count' => 0
		),
		array(
			'id' => 13,
			'title' => 'region5',
			'alias' => 'region5',
			'description' => '',
			'block_count' => 0
		),
		array(
			'id' => 14,
			'title' => 'region6',
			'alias' => 'region6',
			'description' => '',
			'block_count' => 0
		),
		array(
			'id' => 15,
			'title' => 'region7',
			'alias' => 'region7',
			'description' => '',
			'block_count' => 0
		),
		array(
			'id' => 16,
			'title' => 'region8',
			'alias' => 'region8',
			'description' => '',
			'block_count' => 0
		),
		array(
			'id' => 17,
			'title' => 'region9',
			'alias' => 'region9',
			'description' => '',
			'block_count' => 0
		),
	);
}
