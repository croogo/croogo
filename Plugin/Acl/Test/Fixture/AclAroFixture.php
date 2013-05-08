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

App::uses('CakeTestFixture', 'TestSuite/Fixture');

class AclAroFixture extends CakeTestFixture {

	public $name = 'Aro';

/**
 * fields property
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'length' => 10, 'null' => true),
		'model' => array('type' => 'string', 'null' => true),
		'foreign_key' => array('type' => 'integer', 'length' => 10, 'null' => true),
		'alias' => array('type' => 'string', 'default' => ''),
		'lft' => array('type' => 'integer', 'length' => 10, 'null' => true),
		'rght' => array('type' => 'integer', 'length' => 10, 'null' => true)
	);

/**
 * records property
 *
 * @var array
 */
	public $records = array(
		array('parent_id' => null, 'model' => null, 'foreign_key' => null, 'alias' => 'ROOT', 'lft' => 1, 'rght' => 20),
		array('parent_id' => '1', 'model' => 'Department', 'foreign_key' => '1', 'alias' => '', 'lft' => 2, 'rght' => 3),
		array('parent_id' => '1', 'model' => 'Department', 'foreign_key' => '2', 'alias' => '', 'lft' => 4, 'rght' => 7),
		array('parent_id' => '1', 'model' => 'Department', 'foreign_key' => '3', 'alias' => '', 'lft' => 8, 'rght' => 9),
		array('parent_id' => '1', 'model' => 'Department', 'foreign_key' => '4', 'alias' => '', 'lft' => 10, 'rght' => 11),
		array('parent_id' => '1', 'model' => 'Department', 'foreign_key' => '5', 'alias' => '', 'lft' => 12, 'rght' => 13),
		array('parent_id' => '3', 'model' => 'Employee', 'foreign_key' => '1', 'alias' => '', 'lft' => 5, 'rght' => 6),
		array('parent_id' => '1', 'model' => 'Employee', 'foreign_key' => '2', 'alias' => '', 'lft' => 14, 'rght' => 15),
		array('parent_id' => '1', 'model' => 'Employee', 'foreign_key' => '3', 'alias' => '', 'lft' => 16, 'rght' => 17),
		array('parent_id' => '1', 'model' => 'Employee', 'foreign_key' => '4', 'alias' => '', 'lft' => 18, 'rght' => 19),
	);
}
