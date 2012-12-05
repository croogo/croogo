<?php

App::uses('CroogoControllerTestCase', 'TestSuite');
App::uses('CroogoTestFixture', 'TestSuite');
App::uses('AppController', 'Controller');

class TestAppController extends AppController {

	public function admin_edit() {
	}

	public function admin_add() {
	}

	public function register() {
	}

}

class AppControllerTest extends CroogoControllerTestCase {

	public function setUp() {
		parent::setUp();
		$this->generate('TestApp', array(
			'components' => array(
				'Auth',
				'Security',
				'Acl.AclFilter',
				'Blocks.Blocks',
				'Menus.Menus',
				'Taxonomy.Taxonomies',
			)
		));
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->controller);
	}

/**
 * testRenderExistingView
 */
	public function testRenderExistingView() {
		$result = $this->testAction('/admin/test_app/edit', array(
			'return' => 'view',
		));
		$this->assertEquals('admin_edit', trim($result));
	}

/**
 * testRenderAdminFormFallback
 */
	public function testRenderAdminFormFallback() {
		$result = $this->testAction('/admin/test_app/add', array(
			'return' => 'view',
		));
		$this->assertEquals('admin_form', trim($result));
	}

/**
 * testRenderNonEditView
 */
	public function testRenderNonEditView() {
		$result = $this->testAction('/test_app/register', array(
			'return' => 'view',
		));
		$this->assertEquals('register', trim($result));
	}

}
