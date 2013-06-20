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
		$controller = new TheRegionsTestController($request, new CakeResponse());
		$this->View = $this->getMock('View',
			array('element', 'elementExists'),
			array($controller)
		);
		$this->View->loadHelper('Croogo.Layout');
		$this->Regions = $this->getMock('RegionsHelper', array('log'), array($this->View));
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
		$blocksForLayout = array(
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
		$this->Regions->_View->viewVars['blocks_for_layout'] = $blocksForLayout;
		$this->Regions->expects($this->never())->method('log');
		$this->View->expects($this->once())->method('element')
			->with(
				'Blocks.block',
				array('block' => $blocksForLayout['right'][0])
			);
		$result = $this->Regions->blocks('right');
	}

/**
 * testBlocksOptions
 */
	public function testBlocksOptions() {
		$blocksForLayout = array(
			'right' => array(
				0 => array(
					'Block' => array(
						'id' => 1,
						'alias' => 'hello-world',
						'body' => 'hello world',
						'show_title' => false,
						'class' => null,
						'element' => null,
					),
					'Params' => array(
						'enclosure' => false,
					),
				),
			),
		);
		$this->Regions->_View->viewVars['blocks_for_layout'] = $blocksForLayout;
		$this->View->expects($this->once())
			->method('elementExists')
			->will($this->returnValue(true));

		$this->View->expects($this->once())->method('element')
			->with(
				null,
				array('block' => $blocksForLayout['right'][0]),
				array('class' => 'some-class')
			);

		$result = $this->Regions->blocks('right', array(
			'elementOptions' => array('class' => 'some-class')
		));
	}

/**
 * testBlocks with invalid/missing element
 */
	public function testBlockWithInvalidElement() {
		$blocksForLayout = array(
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
		$this->Regions->_View->viewVars['blocks_for_layout'] = $blocksForLayout;
		$this->Regions
			->expects($this->once())
			->method('log')
			->with('Missing element `non-existent` in block `hello-world` (1)');
		$this->View->expects($this->once())
			->method('element')
			->with('Blocks.block', array('block' => $blocksForLayout['right'][0]));
		$result = $this->Regions->blocks('right');
	}

}
