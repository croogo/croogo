<?php

App::uses('CroogoHelper', 'View/Helper');
App::uses('CroogoHtmlHelper', 'View/Helper');
App::uses('Controller', 'Controller');
App::uses('CroogoTestCase', 'TestSuite');
App::uses('View', 'View');
App::uses('HtmlHelper', 'View/Helper');
class CroogoHtmlHelperTest extends CroogoTestCase{

	public function setUp() {
		$controller = null;
		$this->View = new View($controller);
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

	public function testStatusRemove() {
		$result = $this->CroogoHtml->status(0);
		$this->assertContains('<i class="icon-remove red"></i>', $result);
	}

	public function testLink() {
		$result = $this->CroogoHtml->link('', '/remove', array('icon' => 'remove', 'button' => 'danger'));
		$this->assertContains('class="btn btn-danger"', $result);
		$this->assertContains('<i class="icon-remove"></i>', $result);
	}

	public function testLinkDefaultButton() {
		$result = $this->CroogoHtml->link('Remove', '/remove', array('button' => 'default'));
		$this->assertContains('<a href="/remove" class="btn">Remove</a>', $result);
	}

	public function testLinkOptionsIsNull() {
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
		$this->assertTags($result, $expected);
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
		$this->assertTags($result, $expected);
	}

	public function testAddPathAndGetCrumbList() {
		$this->CroogoHtml->addPath('/yes/we/can', '/');
		$result = $this->CroogoHtml->getCrumbList();
		$this->assertContains('<a href="/yes/">yes</a>', $result);
		$this->assertContains('<a href="/yes/we/">we</a>', $result);
		$this->assertContains('<a href="/yes/we/can/">can</a>', $result);
	}
}
