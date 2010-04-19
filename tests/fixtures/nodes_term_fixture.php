<?php

class NodesTermFixture extends CakeTestFixture {
	public $name = 'NodesTerm';
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 20, 'key' => 'primary'),
		'node_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
		'vocabulary_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
		'term_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
		'weight' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	public $records = array(
		array(
			'id' => '6407',
			'node_id' => '21',
			'vocabulary_id' => '0',
			'term_id' => '1',
			'weight' => '',
		),
	);
}

?>