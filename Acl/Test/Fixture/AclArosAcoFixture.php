<?php
/**
 * Short description for file.
 *
 * PHP 5
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/view/1196/Testing>
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/view/1196/Testing CakePHP(tm) Tests
 * @package       Cake.Test.Fixture
 * @since         CakePHP(tm) v 1.2.0.4667
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Croogo\Acl\Test\Fixture;

use Croogo\TestSuite\CroogoTestFixture;

class AclArosAcoFixture extends CroogoTestFixture {

	public $name = 'ArosAco';

/**
 * fields property
 *
 * @var array
 */
	public $fields = array(
		'id' => ['type' => 'integer'],
		'aro_id' => ['type' => 'integer', 'length' => 10, 'null' => false],
		'aco_id' => ['type' => 'integer', 'length' => 10, 'null' => false],
		'_create' => ['type' => 'string', 'length' => 2, 'default' => 0],
		'_read' => ['type' => 'string', 'length' => 2, 'default' => 0],
		'_update' => ['type' => 'string', 'length' => 2, 'default' => 0],
		'_delete' => ['type' => 'string', 'length' => 2, 'default' => 0],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
	);

/**
 * records property
 *
 * @var array
 */
	public $records = array();
}
