<?php

class VocabularyFixture extends CakeTestFixture {
	public $name = 'Vocabulary';
	public $import = 'Vocabulary';
	public $records = array(
		array(
			'id' => '1',
			'title' => 'Categories',
			'alias' => 'categories',
			'description' => '',
			'required' => '0',
			'multiple' => '0',
			'tags' => '0',
			'plugin' => '',
			'term_count' => '2',
			'weight' => '',
			'updated' => '2009-07-22 02:16:21',
			'created' => '2009-07-22 02:16:21',
		),
		array(
			'id' => '2',
			'title' => 'Tags',
			'alias' => 'tags',
			'description' => '',
			'required' => '0',
			'multiple' => '0',
			'tags' => '0',
			'plugin' => '',
			'term_count' => '1',
			'weight' => '',
			'updated' => '2009-07-22 02:16:34',
			'created' => '2009-07-22 02:16:34',
		),
	);
}

?>