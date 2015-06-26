<?php

namespace Croogo\Croogo\Test\TestCase\Controller\Component;

use App\Controller\AppController;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Network\Request;
use Cake\Network\Response;
use Croogo\Core\Controller\Component\CroogoComponent;
use Croogo\Core\TestSuite\CroogoTestCase;
class MockCroogoComponent extends CroogoComponent {

	public function startup(Event $event) {
		$this->_controller = $event->subject();
	}

}

class CroogoTestController extends AppController {
}

class CroogoComponentTest extends CroogoTestCase {

	public $fixtures = array(
//		'plugin.croogo/users.aco',
//		'plugin.croogo/users.aro',
//		'plugin.croogo/users.aros_aco',
		'plugin.croogo/settings.setting',
//		'plugin.croogo/menus.menu',
//		'plugin.croogo/menus.link',
//		'plugin.croogo/users.role',
//		'plugin.croogo/taxonomy.type',
//		'plugin.croogo/taxonomy.vocabulary',
//		'plugin.croogo/taxonomy.types_vocabulary',
		'plugin.croogo/nodes.node',
	);

	public function setUp() {
		parent::setUp();

		$this->Controller = new CroogoTestController(new Request(), new Response());
//		$this->Controller->constructClasses();
		$this->Controller->Croogo = new MockCroogoComponent($this->Controller->components());
		$this->Controller->components()->unload('Blocks');
		$this->Controller->components()->unload('Menus');
		$this->Controller->components()->set('Croogo', $this->Controller->Croogo);
		$this->Controller->startupProcess();
	}

	public function testAddRemoveAcos() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

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
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

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
	public function testRedirect($expected, $url, $data = array(), $indexUrl = array()) {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$Controller = $this->getMock('Croogo\\Croogo\\Test\\TestCase\\Controller\\Component\\CroogoTestController', array('redirect'), array(new Request(), new Response()));
		$Controller->request->data = $data;
		$Controller->expects($this->once())
			->method('redirect')
			->with($this->equalTo($expected));
		$CroogoComponent = new CroogoComponent(new ComponentRegistry());
		$CroogoComponent->startup($Controller);
		$CroogoComponent->redirect($url, null, true, $indexUrl);
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
			array(array('action' => 'index', 1), array('action' => 'edit', 1), array(), array('action' => 'index', 1)),
			array(array('action' => 'edit', 1), array('action' => 'edit', 1), array('apply' => 'Apply'), array('action' => 'index', 1)),
		);
	}

}
