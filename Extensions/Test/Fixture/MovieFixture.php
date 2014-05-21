<?php

namespace Croogo\Extensions\Test\Fixture;

use Croogo\TestSuite\CroogoTestFixture;
class MovieFixture extends CroogoTestFixture {

/**
 * fields property
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'user_id' => array('type' => 'integer', 'null' => true),
		'created' => 'datetime',
		'updated' => 'datetime'
	);

/**
 * records property
 *
 * @var array
 */
	public $records = array(
	);

}
