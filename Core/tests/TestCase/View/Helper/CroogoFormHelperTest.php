<?php
namespace Croogo\Croogo\Test\TestCase\View\Helper;

use Cake\Controller\Controller;
use Cake\View\View;
use Croogo\Core\TestSuite\CroogoTestCase;
use Croogo\Core\View\Helper\CroogoFormHelper;
class CroogoFormHelperTest extends CroogoTestCase {

	/**
	 * @var CroogoFormHelper
	 */
	private $CroogoForm;

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
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

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
		$this->assertHtml($expected, $result);

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
		$this->assertHtml($expected, $result);

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
		$this->assertHtml($expected, $result);
	}

/**
 * testInputAutoTooltips
 */
	public function testInputAutoTooltips() {
		// automatic tooltips
		$result = $this->CroogoForm->input('username', array(
			'label' => false,
			'placeholder' => 'Username',
		));
		$expected = array(
			'div' => array(
				'class',
			),
			'input' => array(
				'name',
				'placeholder',
				'data-placement',
				'data-trigger',
				'data-title',
				'type',
				'id',
			),
			'/div',
		);
		$this->assertHtml($expected, $result);

		// disable auto tooltips
		$result = $this->CroogoForm->input('username', array(
			'label' => false,
			'placeholder' => 'Username',
			'tooltip' => false,
		));
		$expected = array(
			'div' => array(
				'class',
			),
			'input' => array(
				'name',
				'placeholder',
				'type',
				'id',
			),
			'/div',
		);
		$this->assertHtml($expected, $result);
	}

	public function testButtonDefault() {
		$result = $this->CroogoForm->button('Button');
		$expected = array(
			'button' => array(
				'class' => 'btn btn-default',
				'type' => 'submit'
			),
			'Button',
			'/button',
		);
		$this->assertHtml($expected, $result);
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
		$this->assertHtml($expected, $result);
	}

	public function testButtonWithIcon() {
		$result = $this->CroogoForm->button('Button', array('icon' => 'pencil'));

		$expected = array(
			'button' => array(
				'class' => 'btn btn-default',
				'type' => 'submit'
			),
			array(
				'i' => array(
					'class' => 'icon-pencil'
				)
			),
			'/i',
			' Button',
			'/button',
		);
		$this->assertHtml($expected, $result);
	}

	public function testSubmitDefault() {
		$result = $this->CroogoForm->submit('Send');

		$expected = array(
			'div' => array(
				'class' => 'submit',
			),
			array(
				'input' => array(
					'class' => 'btn btn-default',
					'type' => 'submit',
					'value' => 'Send'
				)
			),
			'/div',
		);
		$this->assertHtml($expected, $result);
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
		$this->assertHtml($expected, $result);
	}

	public function testInputPlaceholders() {
		$result = $this->CroogoForm->input('username', array(
			'placeholder' => true,
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
				'placeholder' => 'Username',
				'data-placement' => 'right',
				'data-trigger' => 'focus',
				'data-title' => 'Username',
				'type' => 'text',
				'id',
			),
			'/div',
		);
		$this->assertHtml($expected, $result);

		$result = $this->CroogoForm->input('username', array(
			'placeholder' => 'User/Email',
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
				'placeholder' => 'User/Email',
				'data-placement' => 'right',
				'data-trigger' => 'focus',
				'data-title' => 'User/Email',
				'type' => 'text',
				'id',
			),
			'/div',
		);
		$this->assertHtml($expected, $result);

		$tip = 'Enter your username or email address';
		$result = $this->CroogoForm->input('username', array(
			'placeholder' => 'User/Email',
			'tooltip' => $tip,
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
				'placeholder' => 'User/Email',
				'data-placement' => 'right',
				'data-trigger' => 'focus',
				'data-title' => $tip,
				'type' => 'text',
				'id',
			),
			'/div',
		);
		$this->assertHtml($expected, $result);

		$result = $this->CroogoForm->input('username', array(
			'placeholder' => false,
			'tooltip' => $tip,
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
				'data-title' => $tip,
				'type' => 'text',
				'id',
			),
			'/div',
		);
		$this->assertHtml($expected, $result);
	}

/**
 * testAutocomplete
 */
	public function testAutocomplete() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$result = $this->CroogoForm->autocomplete('user_id', array(
			'autocomplete' => array(
				'data-relatedField' => '#user_id',
				'data-url' => 'http://croogo.org',
			),
		));
		$expected = array(
			array(
				'input' => array(
					'type' => 'hidden',
					'name',
					'id',
				),
			),
			'div' => array(
				'class',
			),
			'label' => array('for'),
			'User Id',
			'/label',
			array(
				'input' => array(
					'name' => 'data[autocomplete_user_id]',
					'data-relatedField' => '#user_id',
					'data-url' => 'http://croogo.org',
					'class' => 'typeahead-autocomplete',
					'autocomplete' => 'off',
					'type' => 'text',
					'id'
				),
			),
			'/div',
		);
		$this->assertHtml($expected, $result);
	}

/**
 * testAutocompleteWithDefault
 */
	public function testAutocompleteWithDefault() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$this->skipIf(env('TRAVIS') == 'true');
		$result = $this->CroogoForm->autocomplete('user_id', array(
			'default' => 3,
			'autocomplete' => array(
				'default' => 'yvonne',
				'data-relatedField' => '#user_id',
				'data-url' => 'http://croogo.org',
			),
		));
		$expected = array(
			array(
				'input' => array(
					'type' => 'hidden',
					'name',
					'value' => 3,
					'id',
				),
			),
			'div' => array(
				'class',
			),
			'label' => array('for'),
			'User Id',
			'/label',
			array(
				'input' => array(
					'name' => 'data[autocomplete_user_id]',
					'data-relatedField' => '#user_id',
					'data-url' => 'http://croogo.org',
					'class' => 'typeahead-autocomplete',
					'autocomplete' => 'off',
					'type' => 'text',
					'value' => 'yvonne',
					'id'
				),
			),
			'/div',
		);
		$this->assertHtml($expected, $result);
	}

/**
 * testAutocompleteWithDefaultFromViewVars
 */
	public function testAutocompleteWithDefaultFromViewVars() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$this->skipIf(env('TRAVIS') == 'true');
		$this->CroogoForm->defaultModel = 'Node';
		$this->View->set('users', array(
			3 => 'yvonne',
		));
		$this->View->request->data = array(
			'Node' => array(
				'id' => 10,
				'user_id' => 3,
			),
		);
		$result = $this->CroogoForm->autocomplete('Node.user_id', array(
			'autocomplete' => array(
				'data-relatedField' => '#NodeUserId',
				'data-displayField' => 'username',
				'data-url' => 'http://croogo.org',
			),
		));
		$expected = array(
			array(
				'input' => array(
					'type' => 'hidden',
					'name',
					'value' => 3,
					'id',
				),
			),
			'div' => array(
				'class',
			),
			'label' => array('for'),
			'User Id',
			'/label',
			array(
				'input' => array(
					'name' => 'data[autocomplete_user_id]',
					'value' => 'yvonne',
					'data-displayField' => 'username',
					'data-url' => 'http://croogo.org',
					'data-relatedField',
					'class',
					'autocomplete',
					'type',
					'id',
				),
			),
			'/div',
		);
		$this->assertHtml($expected, $result);
	}

/**
 * Test placeholder with nested model fields
 */
	public function testInputPlaceholderNestedModel() {
		$expected = array(
			'div' => array(
				'class',
			),
			'label' => array(
				'for',
			),
			'Node',
			'/label',
			'select' => array(
				'name',
				'placeholder' => 'Node',
				'data-placement',
				'data-trigger',
				'data-title',
				'id',
			),
			'/select',
			'/div',
		);
		$result = $this->CroogoForm->input('User.Comment.node_id', array(
			'placeholder' => true,
		));
		$this->assertHtml($expected, $result);
	}

/**
 * Test radio button class
 */
	public function testInputRadioButtonClass() {
		$result = $this->CroogoForm->input('Node.promote', array(
			'type' => 'radio',
			'class' => 'super-radio-button',
			'options' => array(
				0 => 'Not promoted',
				1 => 'Promoted',
			),
		));
		$this->assertStringStartsWith('<div class="input radio">', $result);
		$this->assertContains('class="super-radio-button"', $result);
	}

/**
 * Test checkbox class
 */
	public function testInputCheckboxClass() {
		$result = $this->CroogoForm->input('Node.promote', array(
			'type' => 'checkbox',
			'class' => 'super-checkbox-button',
		));
		$this->assertStringStartsWith('<div class="input checkbox">', $result);
		$this->assertContains('class="super-checkbox-button"', $result);
	}

}
