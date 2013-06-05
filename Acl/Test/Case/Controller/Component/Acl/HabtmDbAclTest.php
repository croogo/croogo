<?php
/**
 * HabtmDbAclTest file.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('ComponentCollection', 'Controller');
App::uses('HabtmDbAcl', 'Acl.Controller/Component/Acl');

class Employee extends CakeTestModel {

	public $hasAndBelongsToMany = array('Department' => array('with' => 'Membership'));

}
/**
 * Test case for AclComponent using the HabtmDbAcl implementation.
 *
 */
class HabtmDbAclTest extends CakeTestCase {

/**
 * fixtures property
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.acl.acl_aro', 'plugin.acl.acl_aco', 'plugin.acl.acl_aros_aco',
		'plugin.acl.employee', 'plugin.acl.department', 'plugin.acl.membership');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Configure::write('Acl.classname', 'HabtmDbAcl');
		Configure::write('Acl.database', 'test');
		$Collection = new ComponentCollection();
		$this->Acl = $Collection->load('Acl', array('habtm' => array(
			'userModel' => 'Employee',
			'groupAlias' => 'Department',
		)));
		$this->_setPermissions();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Acl);
	}

/**
 * _setPermissions method
 *
 * @return void
 */
	protected function _setPermissions() {
		$this->Acl->allow(array('Employee' => array('id' => 1)), 'Controller1', 'read');
		$this->Acl->allow(array('Department' => array('id' => 2)), 'Controller2', 'update');
		$this->Acl->allow(array('Department' => array('id' => 1)), 'Models/User', 'read');
		$this->Acl->allow(array('Department' => array('id' => 3)), 'Controllers/Users/Users', '*');
	}

/**
 * testCheck method
 *
 * @return void
 */
	public function testCheck() {
		$this->assertTrue($this->Acl->check(array('Employee' => array('id' => 1)), 'Controllers/Controller1', 'read'));
		$this->assertFalse($this->Acl->check(array('Employee' => array('id' => 1)), 'Controllers/Controller1', 'create'));

		$this->assertTrue($this->Acl->check(array('Employee' => array('id' => 1)), 'Controllers/Controller2', 'update'));
		$this->assertFalse($this->Acl->check(array('Employee' => array('id' => 1)), 'Controllers/Controller2', 'read'));

		$this->assertTrue($this->Acl->check(array('Employee' => array('id' => 1)), array('model' => 'Employee', 'foreign_key' => 3), 'read'));
		$this->assertFalse($this->Acl->check(array('Employee' => array('id' => 1)), array('model' => 'Employee', 'foreign_key' => 3), 'update'));

		$this->assertTrue($this->Acl->check(array('Employee' => array('id' => 4)), 'Controllers/Users/Users', '*'));
		$this->assertTrue($this->Acl->check(array('Employee' => array('id' => 4)), 'Controllers/Users/Users', 'read'));
		$this->assertTrue($this->Acl->check(array('Employee' => array('id' => 4)), 'Controllers/Users/Users', 'delete'));

		$this->assertTrue($this->Acl->check(array('Department' => array('id' => 3)), 'Controllers/Users/Users', '*'));
		$this->assertTrue($this->Acl->check(array('Department' => array('id' => 3)), 'Controllers/Users/Users', 'read'));
		$this->assertTrue($this->Acl->check(array('Department' => array('id' => 3)), 'Controllers/Users/Users', 'delete'));

		$this->assertFalse($this->Acl->check(array('Employee' => array('id' => 2)), array('model' => 'Employee', 'foreign_key' => 3), 'read'));
	}

}
