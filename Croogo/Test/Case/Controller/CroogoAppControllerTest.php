<?php

App::uses('CroogoControllerTestCase', 'Croogo.TestSuite');
App::uses('CroogoTestFixture', 'Croogo.TestSuite');
App::uses('CroogoAppController', 'Croogo.Controller');
App::uses('File', 'Utility');

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

class CroogoAppControllerTest extends CroogoControllerTestCase {

	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.taxonomy.type',
		'plugin.nodes.node',
	);

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
 * testRenderOverridenAdminFormWithTheme
 */
	public function testRenderOverridenAdminFormWithTheme() {
		$theme = 'Mytheme';
		$this->controller->theme = $theme;
		$filePath = App::themePath($theme) . 'TestApp'. DS . 'admin_edit.ctp';

		$expected = '<h1>I should be displayed</h1>';
		$File = new File($filePath, true, 0777);
		$File->write($expected);
		$File->close();

		$result = $this->testAction('/admin/test_app/edit', array(
			'return' => 'contents',
		));

		$File->delete();
		$this->assertContains($expected, trim($result));
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
		$this->assertContains('btn-group', $result);
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
 * testSetFlashDefaults
 */
	public function testSetFlashDefaults() {
		$this->controller->viewVars = array(
			'displayFields' => array(),
		);
		$this->controller->Session->setFlash('Terms & Conditions', 'flash');
		$result = $this->testAction('/admin/test_app/index', array(
			'return' => 'contents',
		));
		$this->assertContains('Terms &amp; Conditions', $result);
	}

/**
 * testSetFlashUnescaped
 */
	public function testSetFlashUnescaped() {
		$this->controller->viewVars = array(
			'displayFields' => array(),
		);
		$this->controller->Session->setFlash('Terms & Conditions', 'flash', array('escape' => false));
		$result = $this->testAction('/admin/test_app/index', array(
			'return' => 'contents',
		));
		$this->assertContains('Terms & Conditions', $result);
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

/**
 * Test Setup Component
 */
	public function testSetupComponent() {
		$request = new CakeRequest('/api/v1.0/users');
		$request->addParams(array(
			'api' => 'api',
			'prefix' => 'v1.0',
		));

		$controller = new TestAppController($request);
		$defaultComponents = $controller->components;
		$this->assertEmpty($controller->_apiComponents);

		$key = 'Hook.controller_properties.TestApp._apiComponents';
		Configure::write($key, array('BogusApi'));
		Croogo::hookApiComponent('TestApp', 'Example.ImaginaryApi');

		$expected = array(
			'BogusApi' => array(
				'className' => 'BogusApi',
				'priority' => 8,
			),
			'ImaginaryApi' => array(
				'className' => 'Example.ImaginaryApi',
				'priority' => 8,
			),
		);
		$controller = new TestAppController($request);
		$this->assertEquals($expected, $controller->_apiComponents);

		$merged = Hash::merge(
			$defaultComponents,
			array('BogusApi'),
			array('Example.ImaginaryApi' => array('priority' => 8))
		);
		$this->assertEquals($merged, $controller->components);

		Configure::delete('Hook.controller_properties.TestApp');
	}

}
