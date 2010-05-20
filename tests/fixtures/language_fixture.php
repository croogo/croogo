<?php
/* Language Fixture generated on: 2010-05-20 22:05:37 : 1274393797 */
class LanguageFixture extends CakeTestFixture {
	var $name = 'Language';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'native' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'alias' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'status' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'weight' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'title' => 'English',
			'native' => 'English',
			'alias' => 'eng',
			'status' => 1,
			'weight' => 1,
			'updated' => '2009-11-02 21:37:38',
			'created' => '2009-11-02 20:52:00'
		),
	);
}
?>