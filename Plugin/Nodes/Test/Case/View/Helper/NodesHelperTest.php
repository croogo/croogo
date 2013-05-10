<?php

App::uses('NodesHelper', 'Nodes.View/Helper');
App::uses('Controller', 'Controller');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class TheNodesTestController extends Controller {

	public $uses = null;

}

class NodesHelperTest extends CroogoTestCase {

/**
 * setUp
 */
	public function setUp() {
		parent::setUp();

		$request = $this->getMock('CakeRequest');
		$response = $this->getMock('CakeResponse');

		$this->View = new View(new TheNodesTestController($request, $response));
		$this->Nodes = new NodesHelper($this->View);
	}

/**
 * tearDown
 */
	public function tearDown() {
		unset($this->View);
		unset($this->Nodes);
	}

/**
 * Test [node] shortcode
 */
	public function testNodeShortcode() {
		$content = '[node:recent_posts conditions="Node.type:blog" order="Node.id DESC" limit="5"]';
		$this->View->viewVars['nodes_for_layout']['recent_posts'] = array(
			array(
				'Node' => array(
					'id' => 1,
					'title' => 'Hello world',
					'slug' => 'hello-world',
					'type' => 'blog',
				),
			),
		);
		Croogo::dispatchEvent('Helper.Layout.beforeFilter', $this->View, array('content' => &$content));
		$this->assertContains('node-list-recent_posts', $content);
		$this->assertContains('class="node-list"', $content);
	}

}
