<?php

namespace Croogo\Nodes\Test\TestCase\View\Helper;

use Cake\Controller\Controller;
use Croogo\TestSuite\CroogoTestCase;
use Nodes\View\Helper\NodesHelper;
class TheNodesTestController extends Controller {

	public $uses = null;

}

class NodesHelperTest extends CroogoTestCase {

/**
 * setUp
 */
	public function setUp() {
		parent::setUp();

		$request = $this->getMock('Request');
		$response = $this->getMock('Response');

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

/**
 * Test NodesHelper::url()
 */
	public function testNodesUrl() {
		$result = $this->Nodes->url(null);
		$this->assertEquals('/', $result);

		$result = $this->Nodes->url('/page/about');
		$this->assertEquals('/page/about', $result);

		$node = array(
			'Node' => array(
				'type' => 'page',
				'slug' => 'about',
			)
		);
		$result = $this->Nodes->url($node);
		$expected = '/nodes/nodes/view/type:page/slug:about';
		$this->assertEquals($expected, $result);

		$fullBaseUrl = Configure::read('App.fullBaseUrl');
		$result = $this->Nodes->url($node, true);
		$this->assertEquals($fullBaseUrl . $expected, $result);
	}

}
