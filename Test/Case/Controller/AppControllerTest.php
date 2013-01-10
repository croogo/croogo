<?php

App::uses('CroogoControllerTestCase', 'Croogo.TestSuite');
App::uses('CroogoTestFixture', 'Croogo.TestSuite');
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

	public $fixtures = array(
		'plugin.translate.i18n',
		'plugin.croogo.aco',
		'plugin.croogo.aro',
		'plugin.croogo.aros_aco',
		'plugin.blocks.block',
		'plugin.comments.comment',
		'plugin.contacts.contact',
		'plugin.translate.i18n',
		'plugin.settings.language',
		'plugin.menus.link',
		'plugin.menus.menu',
		'plugin.contacts.message',
		'plugin.nodes.node',
		'plugin.meta.meta',
		'plugin.taxonomy.nodes_taxonomy',
		'plugin.blocks.region',
		'plugin.users.role',
		'plugin.settings.setting',
		'plugin.taxonomy.taxonomy',
		'plugin.taxonomy.term',
		'plugin.taxonomy.type',
		'plugin.taxonomy.types_vocabulary',
		'plugin.users.user',
		'plugin.taxonomy.vocabulary',
	);

	public function setUp() {
		parent::setUp();
		$TestApp = $this->generate('TestApp', array(
			'components' => array(
				'Auth',
				'Security',
				'Acl.AclFilter',
				'Blocks.Blocks',
				'Menus.Menus',
				'Taxonomy.Taxonomies',
			)
		));
		$TestApp->helpers[] = 'Croogo.Croogo';
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
