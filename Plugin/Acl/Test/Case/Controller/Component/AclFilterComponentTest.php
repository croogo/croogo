<?php

App::uses('Controller', 'Controller');
App::uses('CroogoTestCase', 'TestSuite');

class AclFilterTestController extends Controller {

	public $components = array(
		'Auth',
		'Acl',
		'Session',
		'Acl.AclFilter',
		);

}

class AclFilterComponentTest extends CroogoTestCase {

	public $fixtures = array(
		'app.aro',
		'app.aco',
		'app.aros_aco',
		'plugin.users.user',
		'plugin.users.role',
		'plugin.settings.setting',
		);

	public function testAllowedActions() {
		$request = new CakeRequest('/users/view/yvonne');
		$request->addParams(array(
			'controller' => 'users',
			'action' => 'view',
			));
		$response = $this->getMock('CakeRequest');
		$this->Controller = new AclFilterTestController($request, $response);
		$this->Controller->name = 'Users';
		$this->Controller->constructClasses();
		$this->Controller->startupProcess();
		$this->Controller->AclFilter->auth();
		$result = $this->Controller->Auth->allowedActions;
		$this->assertTrue(in_array('view', $result));
	}

	public function testPrefixedAllowedActions() {
		$request = new CakeRequest('/admin/users/view/3');
		$request->addParams(array(
			'admin' => true,
			'controller' => 'users',
			'action' => 'admin_add',
			3,
			));
		$response = $this->getMock('CakeRequest');
		$this->Controller = new AclFilterTestController($request, $response);
		$this->Controller->constructClasses();
		$user = array(
			'id' => 3,
			'role_id' => 3,
			'username' => 'yvonne',
			);
		$this->Controller->Session->write('Auth.User', $user);

		$aro = array('Role' => array('id' => 3));
		$aco = 'controllers/Users/admin_add';

		// Role.3 has no access to Users/admin_add yet
		$allowed = $this->Controller->Acl->check($aro, $aco);
		$this->assertEquals(false, $allowed);

		// grant access to /admin/users/view to Role.3
		$this->Controller->Acl->allow($aro, $aco);

		// new permission active
		$allowed = $this->Controller->Acl->check($aro, $aco);
		$this->assertEquals(true, $allowed);
	}

}
