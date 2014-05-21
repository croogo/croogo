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
		'id' => ['type' => 'integer'],
		'title' => ['type' => 'string', 'null' => false],
		'year' => ['type' => 'integer', 'null' => false],
		'user_id' => ['type' => 'integer', 'null' => true],
		'created' => 'datetime',
		'updated' => 'datetime',
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
	);

/**
 * records property
 *
 * @var array
 */
	public $records = array(
	);

}
