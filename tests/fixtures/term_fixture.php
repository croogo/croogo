<?php

class TermFixture extends CakeTestFixture {
	public $name = 'Term';
	public $import = 'Term';
	public $records = array(
		array(
			'id' => '1',
			'parent_id' => '',
			'vocabulary_id' => '1',
			'title' => 'Uncategorized',
			'slug' => 'uncategorized',
			'description' => '',
			'lft' => '1',
			'rght' => '2',
			'status' => '1',
			'updated' => '2009-07-22 03:38:43',
			'created' => '2009-07-22 03:34:56',
		),
		array(
			'id' => '2',
			'parent_id' => '',
			'vocabulary_id' => '1',
			'title' => 'Announcements',
			'slug' => 'announcements',
			'description' => '',
			'lft' => '3',
			'rght' => '8',
			'status' => '1',
			'updated' => '2009-07-22 03:45:37',
			'created' => '2009-07-22 03:45:37',
		),
		array(
			'id' => '6',
			'parent_id' => '',
			'vocabulary_id' => '2',
			'title' => 'mytag',
			'slug' => 'mytag',
			'description' => '',
			'lft' => '9',
			'rght' => '10',
			'status' => '1',
			'updated' => '2009-08-26 14:42:43',
			'created' => '2009-08-26 14:42:43',
		),
		array(
			'id' => '7',
			'parent_id' => '',
			'vocabulary_id' => '3',
			'title' => 'test term',
			'slug' => 'test-term-1',
			'description' => '',
			'lft' => '11',
			'rght' => '12',
			'status' => '1',
			'updated' => '2009-09-02 19:27:26',
			'created' => '2009-09-02 19:27:26',
		),
	);
}

?>