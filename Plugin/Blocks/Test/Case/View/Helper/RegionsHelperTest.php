<?php
App::uses('RegionsHelper', 'Blocks.View/Helper');
App::uses('LayoutHelper', 'Croogo.View/Helper');
App::uses('SessionComponent', 'Controller/Component');
App::uses('Controller', 'Controller');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class TheRegionsTestController extends Controller {

	public $components = array();

	public $uses = null;

}

class RegionsHelperTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.settings.setting',
	);

/**
 * setUp
 */
	public function setUp() {
		parent::setUp();
		$this->ComponentCollection = new ComponentCollection();

		$request = new CakeRequest('nodes/nodes/index');
		$request->params = array(
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'index',
			'named' => array(),
		);
		$view = new View(new TheRegionsTestController($request, new CakeResponse()));
		$view->loadHelper('Croogo.Layout');
		$this->Regions = $this->getMock('RegionsHelper', array('log'), array($view));
		$this->_appEncoding = Configure::read('App.encoding');
		$this->_asset = Configure::read('Asset');
		$this->_debug = Configure::read('debug');
	}

/**
 * tearDown
 */
	public function tearDown() {
		Configure::write('App.encoding', $this->_appEncoding);
		Configure::write('Asset', $this->_asset);
		Configure::write('debug', $this->_debug);
		ClassRegistry::flush();
		unset($this->Regions);
	}

/**
 * testIsEmpty
 */
	public function testIsEmpty() {
		$this->assertTrue($this->Regions->isEmpty('right'));
		$this->Regions->_View->viewVars['blocks_for_layout'] = array(
			'right' => array(
				'0' => array('block here'),
				'1' => array('block here'),
				'2' => array('block here'),
			),
		);
		$this->assertFalse($this->Regions->isEmpty('right'));
	}

/**
 * testBlocks
 */
	public function testBlocks() {
		$this->Regions->_View->viewVars['blocks_for_layout'] = array(
			'right' => array(
				0 => array(
					'Block' => array(
						'id' => 1,
						'alias' => 'hello-world',
						'body' => 'hello world',
						'show_title' => false,
						'class' => null,
						'element' => null,
					)
				),
			),
		);
		$this->Regions->expects($this->never())->method('log');
		$result = $this->Regions->blocks('right');
		$this->assertContains('id="block-1"', $result);
		$this->assertContains('block-hello-world', $result);
		$this->assertContains('hello world', $result);
	}

/**
 * testBlocks with invalid/missing element
 */
	public function testBlockWithInvalidElement() {
		$this->Regions->_View->viewVars['blocks_for_layout'] = array(
			'right' => array(
				0 => array(
					'Block' => array(
						'id' => 1,
						'alias' => 'hello-world',
						'body' => 'hello world',
						'show_title' => false,
						'class' => null,
						'element' => 'non-existent',
					)
				),
			),
		);
		$this->Regions
			->expects($this->once())
			->method('log')
			->with('Missing element `non-existent` in block `hello-world` (1)');
		$result = $this->Regions->blocks('right');
		$this->assertContains('id="block-1"', $result);
		$this->assertContains('block-hello-world', $result);
		$this->assertContains('hello world', $result);
	}

}
