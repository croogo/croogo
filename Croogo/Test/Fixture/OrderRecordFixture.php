<?php

namespace Croogo\Croogo\Test\Fixture;

use Croogo\TestSuite\CroogoTestFixture;
class OrderRecordFixture extends CroogoTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 60),
		'weight' => array('type' => 'integer', 'null' => false, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'start' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'end' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
		array(
			'id' => 1,
			'title' => 'Random record',
			'weight' => 1,
			'updated' => '2009-11-02 21:37:38',
			'created' => '2009-11-02 20:52:00'
		),
		array(
			'id' => 2,
			'title' => 'Second record',
			'weight' => 1,
			'updated' => '2009-11-02 21:37:38',
			'created' => '2009-11-02 20:52:00',
			'start' => '2014-01-31 07:00:00',
			'end' => '2014-01-31 08:00:00',
		),
		array(
			'id' => 3,
			'title' => 'Third record',
			'weight' => 1,
			'updated' => '2009-11-02 21:37:38',
			'created' => '2009-11-02 20:52:00',
			'start' => '2014-01-31 07:10:00',
			'end' => '2014-01-31 08:00:00',
		),
		array(
			'id' => 4,
			'title' => 'Fourth record',
			'weight' => 1,
			'updated' => '2009-11-02 21:37:38',
			'created' => '2009-11-02 20:52:00',
			'start' => '2014-01-31 09:10:00',
		),
		array(
			'id' => 5,
			'title' => 'Fifth record',
			'weight' => 1,
			'updated' => '2009-11-02 21:37:38',
			'created' => '2009-11-02 20:52:00',
			'start' => '2014-01-31 09:12:00',
			'end' => '2014-01-31 09:15:00',
		),
	);

}
