<?php
App::uses('CroogoFormHelper', 'View/Helper');
App::uses('Controller', 'Controller');
App::uses('CroogoTestCase', 'TestSuite');

class CroogoFormHelperTest extends CroogoTestCase{

	public function setUp() {
		$controller = null;
		$this->View = new View($controller);
		$this->CroogoForm = new CroogoFormHelper($this->View);
	}

	public function tearDown() {
		unset($this->View);
		unset($this->CroogoHtml);
	}

	public function testButtonDefault() {
		$result = $this->CroogoForm->button('Button');

		$expected = array(
			'button' => array(
				'class' => 'btn',
				'type' => 'submit'
			),
			'Button',
			'/button',
		);
		$this->assertTags($result, $expected);
	}

	public function testButtonDanger() {
		$result = $this->CroogoForm->button('Button', array('button' => 'danger'));

		$expected = array(
			'button' => array(
				'class' => 'btn btn-danger',
				'type' => 'submit'
			),
			'Button',
			'/button',
		);
		$this->assertTags($result, $expected);
	}

	public function testButtonWithIcon() {
		$result = $this->CroogoForm->button('Button', array('icon' => 'pencil'));

		$expected = array(
			'button' => array(
				'class' => 'btn',
				'type' => 'submit'
			),
			array(
				'i' => array(
					'class' => 'icon-pencil'
				)
			),
			'/i',
			'  Button',
			'/button',
		);
		$this->assertTags($result, $expected);
	}

	public function testSubmitDefault() {
		$result = $this->CroogoForm->submit('Send');

		$expected = array(
			'div' => array(
				'class' => 'submit',
			),
			array(
				'input' => array(
					'class' => 'btn',
					'type' => 'submit',
					'value' => 'Send'
				)
			),
			'/div',
		);
		$this->assertTags($result, $expected);
	}

	public function testSubmitDanger() {
		$result = $this->CroogoForm->submit('Send', array('button' => 'danger'));

		$expected = array(
			'div' => array(
				'class' => 'submit',
			),
			array(
				'input' => array(
					'class' => 'btn btn-danger',
					'type' => 'submit',
					'value' => 'Send'
				)
			),
			'/div',
		);
		$this->assertTags($result, $expected);
	}

}
