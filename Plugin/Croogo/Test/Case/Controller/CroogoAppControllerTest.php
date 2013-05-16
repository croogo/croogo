<?php

App::uses('CroogoControllerTestCase', 'Croogo.TestSuite');
App::uses('CroogoTestFixture', 'Croogo.TestSuite');
App::uses('CroogoAppController', 'Croogo.Controller');

class TestAppController extends CroogoAppController {

	public function admin_edit() {
	}

	public function admin_add() {
	}

	public function register() {
	}

	public function admin_index() {
	}

	public function admin_index_no_actions() {
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

/**
 * testRenderDefaultActionsBlock
 */
	public function testRenderDefaultActionsBlock() {
		$this->controller->viewVars = array(
			'displayFields' => array(),
		);
		$result = $this->testAction('/admin/test_app/index', array(
			'return' => 'view',
		));
		$this->assertContains('nav-buttons', $result);
	}

/**
 * testRenderNoActionsBlock
 */
	public function testRenderNoActionsBlock() {
		$this->controller->viewVars = array(
			'displayFields' => array(),
		);
		$result = $this->testAction('/admin/test_app/index_no_actions', array(
			'return' => 'view',
		));
		$this->assertNotContains('nav-buttons', $result);
	}

/**
 * testPaginatorIsNotLoadedWithoutCache
 */
	public function testPaginatorIsNotLoadedWithoutCache() {
		$this->controller->viewVars = array(
			'displayFields' => array(),
		);
		$this->assertFalse(in_array('Paginator', $this->controller->helpers));
		$this->controller->usePaginationCache = false;
		$result = $this->testAction('/admin/test_app/index', array(
			'return' => 'view',
		));
		$this->assertFalse(in_array('Paginator', $this->controller->helpers));
	}

/**
 * testPaginatorIsLoadedWithCache
 */
	public function testPaginatorIsLoadedWithCache() {
		$this->controller->viewVars = array(
			'displayFields' => array(),
		);
		$this->assertFalse(in_array('Paginator', $this->controller->helpers));
		$this->controller->usePaginationCache = true;
		$result = $this->testAction('/admin/test_app/index', array(
			'return' => 'view',
		));
		$this->assertTrue(in_array('Paginator', $this->controller->helpers));
	}

}
