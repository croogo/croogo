<?php

App::uses('Component', 'Controller');
App::uses('AppController', 'Controller');
App::uses('CroogoControllerTestCase', 'TestSuite');
app::uses('CroogoComponent', 'Controller/Component');

class MockCroogoComponent extends CroogoComponent {

	public function startup(Controller $controller) {
		$this->controller = $controller;
		$this->_CroogoPlugin = new CroogoPlugin();
		$this->_CroogoPlugin->Setting->writeConfiguration();
	}

}

class CroogoTestController extends AppController {
}

class CroogoComponentTest extends CroogoControllerTestCase {

	public $fixtures = array(
		'app.aco',
		'app.aro',
		'app.aros_aco',
		'plugin.settings.setting',
		'plugin.menus.menu',
		'plugin.menus.link',
		'plugin.users.role',
		'plugin.taxonomy.type',
		'plugin.taxonomy.vocabulary',
		'plugin.taxonomy.types_vocabulary',
	);

	public function setUp() {
		parent::setUp();

		$this->Controller = new CroogoTestController(new CakeRequest(), new CakeResponse());
		$this->Controller->constructClasses();
		$this->Controller->Croogo = new MockCroogoComponent($this->Controller->Components);
		$this->Controller->Components->unload('Blocks');
		$this->Controller->Components->unload('Menus');
		$this->Controller->Components->set('Croogo', $this->Controller->Croogo);
		$this->Controller->startupProcess();
	}

	public function testAddRemoveAcos() {
		$Aco = ClassRegistry::init('Aco');

		$this->Controller->Croogo->addAco('CroogoTestController');
		$parent = $Aco->findByAlias('CroogoTestController');
		$this->assertNotEmpty($parent);

		$this->Controller->Croogo->addAco('CroogoTestController/index');
		$child = $Aco->findByParentId($parent['Aco']['id']);
		$this->assertNotEmpty($child);

		$this->Controller->Croogo->removeAco('CroogoTestController/index');
		$child = $Aco->findByParentId($parent['Aco']['id']);
		$this->assertEmpty($child);

		$this->Controller->Croogo->removeAco('CroogoTestController');
		$parent = $Aco->findByAlias('CroogoTestController');
		$this->assertEmpty($parent);
	}

	public function testPluginIsActive() {
		$result = $this->Controller->Croogo->pluginIsActive('Example');
		$this->assertTrue($result);
		$result = $this->Controller->Croogo->pluginIsActive('example');
		$this->assertTrue($result);
		$result = $this->Controller->Croogo->pluginIsActive('Shops');
		$this->assertFalse($result);
	}

/**
 * testTitleForLayout
 *
 * @dataProvider titleForLayoutData
 */
	public function testTitleForLayout($expected, $params) {
		$this->Controller->request->params = $params;
		$this->Controller->Croogo->beforeRender($this->Controller);
		$this->assertEquals(__($expected), $this->Controller->viewVars['title_for_layout']);
	}

/**
 * titleForLayoutData
 */
	public function titleForLayoutData() {
		return array(
			array('Actions', array('plugin' => 'acl', 'controller' => 'acl_actions', 'action' => 'admin_index')),
			array('Upload', array('plugin' => 'file_manager', 'controller' => 'file_manager', 'action' => 'admin_upload')),
			array('Blocks', array('plugin' => 'blocks', 'controller' => 'blocks', 'action' => 'admin_index')),
			array('Add Block', array('plugin' => 'blocks', 'controller' => 'blocks', 'action' => 'admin_add')),
			array('Edit Block', array('plugin' => 'blocks', 'controller' => 'blocks', 'action' => 'admin_edit')),
		);
	}

}
