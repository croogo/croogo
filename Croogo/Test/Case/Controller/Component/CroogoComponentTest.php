<?php

App::uses('CroogoComponent', 'Controller/Component');
App::uses('Component', 'Controller');
app::uses('ComponentCollection', 'Controller/Component');
App::uses('AppController', 'Controller');
App::uses('CroogoTestCase', 'Croogo.TestSuite');
app::uses('CroogoComponent', 'Croogo.Controller/Component');

class MockCroogoComponent extends CroogoComponent {

	public function startup(Controller $controller) {
		$this->_controller = $controller;
		$this->_CroogoPlugin = new CroogoPlugin();
		$this->_CroogoPlugin->Setting->writeConfiguration();
	}

}

class CroogoTestController extends AppController {
}

class CroogoComponentTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.users.aco',
		'plugin.users.aro',
		'plugin.users.aros_aco',
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
 * testRedirect
 *
 * @return void
 * @dataProvider redirectData
 */
	public function testRedirect($expected, $url, $data = array()) {
		$Controller = $this->getMock('CroogoTestController', array('redirect'), array(new CakeRequest(), new CakeResponse()));
		$Controller->request->data = $data;
		$Controller->expects($this->once())
			->method('redirect')
			->with($this->equalTo($expected));
		$CroogoComponent = new CroogoComponent(new ComponentCollection());
		$CroogoComponent->startup($Controller);
		$CroogoComponent->redirect($url);
	}

/**
 * redirectData
 *
 * @return array
 */
	public function redirectData() {
		return array(
			array('croogo.org', 'croogo.org'),
			array(array('action' => 'index'), array('action' => 'edit', 1)),
			array(array('action' => 'edit', 1), array('action' => 'edit', 1), array('apply' => 'Apply')),
		);
	}

}
