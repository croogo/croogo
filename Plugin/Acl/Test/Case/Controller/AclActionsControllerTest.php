<?php
App::uses('CroogoTestCase', 'TestSuite');
App::uses('Router', 'Routing');
App::uses('CroogoComponent', 'Controller/Component');
App::uses('AclAppController', 'Acl.Controller');
App::uses('AclActionsController', 'Acl.Controller');
App::uses('AclFilterComponent', 'Acl.Controller/Component');

class AclActionsTestController extends AclActionsController {

	protected function _stop($status = 0) {
		$this->stopped = $status;
	}

}

class AclActionsControllerTest extends CroogoTestCase {

	public $AclActions = null;

	public $fixtures = array(
		'app.aro',
		'app.aco',
		'app.aros_aco',
		);

	public function setUp() {
		parent::setUp();
		$request = new CakeRequest();
		$response = new CakeResponse();
		$this->AclActions = new AclActionsTestController($request, $response);
		$this->AclActions->constructClasses();
		$this->AclActions->Components->unload('Croogo');
	}

	public function testGenerateActions() {
		$this->AclActions->request->addParams(array(
			'prefix' => 'admin',
			'admin' => true,
			'plugin' => 'acl',
			'controller' => 'acl_actions',
			'action' => 'admin_generate',
			'named' => array(
				'permissions' => 1,
				),
			'pass' => array(),
			));
		$this->AclActions->startupProcess();

		// remove some nodes so that it's recreated
		$node = $this->AclActions->Acl->Aco->node('controllers/Nodes');
		$this->assertNotEmpty($node);
		$this->AclActions->Acl->Aco->removeFromTree($node[0]['Aco']['id']);

		$response = $this->AclActions->invokeAction($this->AclActions->request);
		$result = $this->AclActions->Session->read('Message.flash');
		preg_match('/Created ([0-9]+) new permissions/', $result['message'], $matches);
		$this->assertTrue(isset($matches[1]));
		$this->assertTrue($matches[1] == 18);
	}

}
