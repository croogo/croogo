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

	public function testInputTooltips() {
		$result = $this->CroogoForm->input('username', array(
			'tooltip' => 'Username',
		));
		$expected = array(
			'div' => array(
				'class',
				),
			'label' => array('for' => 'username'),
			'Username',
			'/label',
			'input' => array(
				'name',
				'data-placement' => 'right',
				'data-trigger' => 'focus',
				'data-title' => 'Username',
				'type' => 'text',
				'id',
				),
			'/div',
		);
		$this->assertTags($result, $expected);

		$result = $this->CroogoForm->input('username', array(
			'tooltip' => array(
				'data-title' => 'Username',
				'data-placement' => 'left',
				'data-trigger' => 'click',
			)
		));
		$expected = array(
			'div' => array(
				'class',
				),
			'label' => array('for' => 'username'),
			'Username',
			'/label',
			'input' => array(
				'name',
				'data-placement' => 'left',
				'data-trigger' => 'click',
				'data-title' => 'Username',
				'type' => 'text',
				'id',
				),
			'/div',
		);
		$this->assertTags($result, $expected);

		$result = $this->CroogoForm->input('username', array(
			'hiddenField' => false,
			'type' => 'checkbox',
			'tooltip' => array(
				'data-title' => 'Username',
				'data-placement' => 'left',
				'data-trigger' => 'click',
			)
		));
		$expected = array(
			'div' => array(
				'class',
				'data-placement' => 'left',
				'data-trigger' => 'click',
				'data-title' => 'Username',
				),
			'input' => array(
				'type' => 'checkbox',
				'name',
				'value',
				'id',
				),
			'label' => array('for' => 'username'),
			'Username',
			'/label',
			'/div',
		);
		$this->assertTags($result, $expected);
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
