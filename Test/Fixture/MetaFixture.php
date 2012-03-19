<?php
/* Meta Fixture generated on: 2010-05-20 22:05:44 : 1274393804 */
class MetaFixture extends CroogoTestFixture {
	public $name = 'Meta';

	public $table = 'meta';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 20, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => false, 'default' => 'Node'),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 20),
		'key' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'value' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'weight' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		array(
			'id' => 1,
			'model' => 'Node',
			'foreign_key' => 1,
			'key' => 'meta_keywords',
			'value' => 'key1, key2',
			'weight' => NULL
		),
	);
}
