<?php

namespace Croogo\Core\Test\TestCase\View\Helper;

use Cake\Controller\Controller;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\View\View;
use Croogo\Core\TestSuite\CroogoTestCase;
use Croogo\Core\View\Helper\CroogoHtmlHelper;
class CroogoHtmlHelperTest extends CroogoTestCase {

	public $fixtures = array(
//		'plugin.taxonomy.type',
	);

	/**
	 * @var CroogoHtmlHelper
	 */
	private $CroogoHtml;

	public function setUp() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$controller = null;
		$this->View = new View(new Request, new Response);
		$this->CroogoHtml = new CroogoHtmlHelper($this->View);
	}

	public function tearDown() {
		unset($this->View);
		unset($this->CroogoHtml);
	}

	public function testIcon() {
		$result = $this->CroogoHtml->icon('remove');
		$this->assertContains('<i class="icon-remove"></i>', $result);
	}

	public function testStatusOk() {
		$result = $this->CroogoHtml->status(1);
		$this->assertContains('<i class="icon-ok green"></i>', $result);
	}

	public function testStatusOkWithUrl() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$result = $this->CroogoHtml->status(1, array(
			'prefix' => 'admin',
			'plugin' => 'Croogo/Nodes',
			'controller' => 'Nodes',
			'action' => 'toggle',
		));
		$expected = array(
			'a' => array(
				'href',
				'data-url' => '/admin/nodes/nodes/toggle',
				'class' => 'icon-ok green ajax-toggle',
			),
			'/a',
		);
		$this->assertHtml($expected, $result);
	}

	public function testStatusRemove() {
		$result = $this->CroogoHtml->status(0);
		$this->assertContains('<i class="icon-remove red"></i>', $result);
	}

	public function testStatusRemoveWithUrl() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$result = $this->CroogoHtml->status(0, array(
			'prefix' => 'admin',
			'plugin' => 'Croogo/Nodes',
			'controller' => 'Nodes',
			'action' => 'delete',
		));
		$expected = array(
			'a' => array(
				'href',
				'data-url' => '/admin/nodes/nodes/delete',
				'class' => 'icon-remove red ajax-toggle',
			),
			'/a',
		);
		$this->assertHtml($expected, $result);
	}

	public function testLink() {
		$result = $this->CroogoHtml->link('', '/remove', array('icon' => 'remove', 'button' => 'danger'));
		$this->assertContains('class="btn btn-danger"', $result);
		$this->assertContains('<i class="icon-remove icon-large"></i>', $result);
	}

/**
 * testLinkWithSmallIcon
 */
	public function testLinkWithSmallIcon() {
		$result = $this->CroogoHtml->link('', '/remove', array(
			'icon' => 'remove',
			'iconSize' => 'small',
			'button' => 'danger'
		));
		$this->assertContains('class="btn btn-danger"', $result);
		$this->assertContains('<i class="icon-remove"></i>', $result);
	}

/**
 * testLinkWithInlineIcon
 */
	public function testLinkWithInlineIcon() {
		$result = $this->CroogoHtml->link('', '/remove', array(
			'icon' => 'remove',
			'iconSize' => 'small',
			'iconInline' => true,
			'button' => 'danger'
		));
		$expected = array(
			'a' => array(
				'href',
				'class' => 'btn btn-danger icon-remove',
			),
		);
		$this->assertHtml($expected, $result);

		$result = $this->CroogoHtml->link('', '/remove', array(
			'icon' => 'remove',
			'iconInline' => true,
			'button' => 'danger'
		));
		$expected = array(
			'a' => array(
				'href',
				'class' => 'btn btn-danger icon-large icon-remove',
			),
		);
		$this->assertHtml($expected, $result);
	}

	public function testLinkDefaultButton() {
		$result = $this->CroogoHtml->link('Remove', '/remove', array('button' => 'default'));
		$this->assertContains('<a href="/remove" class="btn btn-default">Remove</a>', $result);
	}

	public function testLinkOptionsIsNull() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$result = $this->CroogoHtml->link('Remove', '/remove', null);
	}

	public function testLinkTooltip() {
		$result = $this->CroogoHtml->link('', '/remove', array('tooltip' => 'remove it'));
		$expected = array(
			'a' => array(
				'href',
				'rel' => 'tooltip',
				'data-placement',
				'data-trigger',
				'data-title' => 'remove it',
			),
			'/a',
		);
		$this->assertHtml($expected, $result);
	}

	public function testLinkButtonTooltipWithArrayOptions() {
		$result = $this->CroogoHtml->link('', '/remove', array(
			'button' => array('success'),
			'tooltip' => array(
				'data-title' => 'remove it',
				'data-placement' => 'left',
				'data-trigger' => 'focus',
			),
		));
		$expected = array(
			'a' => array(
				'href',
				'class' => 'btn btn-success',
				'rel' => 'tooltip',
				'data-placement' => 'left',
				'data-trigger' => 'focus',
				'data-title' => 'remove it',
			),
			'/a',
		);
		$this->assertHtml($expected, $result);
	}

	public function testAddPathAndGetCrumbList() {
		$this->CroogoHtml->addPath('/yes/we/can', '/');
		$result = $this->CroogoHtml->getCrumbList();
		$this->assertContains('<a href="/yes/">yes</a>', $result);
		$this->assertContains('<a href="/yes/we/">we</a>', $result);
		$this->assertContains('<a href="/yes/we/can/">can</a>', $result);
	}
}
