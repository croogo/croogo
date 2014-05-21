<?php

namespace Croogo\Taxonomy\Test\Fixture;
class TermFixture extends CroogoTestFixture {

	public $name = 'Term';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
		'title' => ['type' => 'string', 'null' => false, 'default' => null],
		'slug' => ['type' => 'string', 'null' => false, 'default' => null],
		'description' => ['type' => 'text', 'null' => true, 'default' => null],
		'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'PRIMARY' => ['type' => 'unique', 'columns' => 'id'], 'term_slug' => ['type' => 'unique', 'columns' => 'slug']],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
		array(
			'id' => 1,
			'title' => 'Uncategorized',
			'slug' => 'uncategorized',
			'description' => '',
			'updated' => '2009-07-22 03:38:43',
			'created' => '2009-07-22 03:34:56'
		),
		array(
			'id' => 2,
			'title' => 'Announcements',
			'slug' => 'announcements',
			'description' => '',
			'updated' => '2010-05-16 23:57:06',
			'created' => '2009-07-22 03:45:37'
		),
		array(
			'id' => 3,
			'title' => 'mytag',
			'slug' => 'mytag',
			'description' => '',
			'updated' => '2009-08-26 14:42:43',
			'created' => '2009-08-26 14:42:43'
		),
	);
}
