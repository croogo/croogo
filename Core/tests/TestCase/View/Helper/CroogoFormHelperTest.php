<?php
namespace Croogo\Core\Test\TestCase\View\Helper;

use Cake\Controller\Controller;
use Cake\View\View;
use Croogo\Core\TestSuite\CroogoTestCase;
use Croogo\Core\View\Helper\CroogoFormHelper;

class CroogoFormHelperTest extends CroogoTestCase
{

    /**
     * @var CroogoFormHelper
     */
    private $CroogoForm;

    public function setUp()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $controller = null;
        $this->View = new View($controller);
        $this->CroogoForm = new CroogoFormHelper($this->View);
    }

    public function tearDown()
    {
        unset($this->View);
        unset($this->CroogoHtml);
    }

    public function testInputTooltips()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $result = $this->CroogoForm->input('username', [
            'tooltip' => 'Username',
        ]);
        $expected = [
            'div' => [
                'class',
            ],
            'label' => ['for' => 'username'],
            'Username',
            '/label',
            'input' => [
                'name',
                'data-placement' => 'right',
                'data-trigger' => 'focus',
                'data-title' => 'Username',
                'type' => 'text',
                'id',
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->CroogoForm->input('username', [
            'tooltip' => [
                'data-title' => 'Username',
                'data-placement' => 'left',
                'data-trigger' => 'click',
            ]
        ]);
        $expected = [
            'div' => [
                'class',
            ],
            'label' => ['for' => 'username'],
            'Username',
            '/label',
            'input' => [
                'name',
                'data-placement' => 'left',
                'data-trigger' => 'click',
                'data-title' => 'Username',
                'type' => 'text',
                'id',
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->CroogoForm->input('username', [
            'hiddenField' => false,
            'type' => 'checkbox',
            'tooltip' => [
                'data-title' => 'Username',
                'data-placement' => 'left',
                'data-trigger' => 'click',
            ]
        ]);
        $expected = [
            'div' => [
                'class',
                'data-placement' => 'left',
                'data-trigger' => 'click',
                'data-title' => 'Username',
            ],
            'input' => [
                'type' => 'checkbox',
                'name',
                'value',
                'id',
            ],
            'label' => ['for' => 'username'],
            'Username',
            '/label',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

/**
 * testInputAutoTooltips
 */
    public function testInputAutoTooltips()
    {
        // automatic tooltips
        $result = $this->CroogoForm->input('username', [
            'label' => false,
            'placeholder' => 'Username',
        ]);
        $expected = [
            'div' => [
                'class',
            ],
            'input' => [
                'name',
                'placeholder',
                'data-placement',
                'data-trigger',
                'data-title',
                'type',
                'id',
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        // disable auto tooltips
        $result = $this->CroogoForm->input('username', [
            'label' => false,
            'placeholder' => 'Username',
            'tooltip' => false,
        ]);
        $expected = [
            'div' => [
                'class',
            ],
            'input' => [
                'name',
                'placeholder',
                'type',
                'id',
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    public function testButtonDefault()
    {
        $result = $this->CroogoForm->button('Button');
        $expected = [
            'button' => [
                'class' => 'btn',
                'type' => 'submit'
            ],
            'Button',
            '/button',
        ];
        $this->assertHtml($expected, $result);
    }

    public function testButtonDanger()
    {
        $result = $this->CroogoForm->button('Button', ['button' => 'danger']);

        $expected = [
            'button' => [
                'class' => 'btn btn-danger',
                'type' => 'submit'
            ],
            'Button',
            '/button',
        ];
        $this->assertHtml($expected, $result);
    }

    public function testButtonWithIcon()
    {
        $result = $this->CroogoForm->button('Button', ['icon' => 'pencil']);

        $expected = [
            'button' => [
                'class' => 'btn',
                'type' => 'submit'
            ],
            [
                'i' => [
                    'class' => 'icon-pencil'
                ]
            ],
            '/i',
            ' Button',
            '/button',
        ];
        $this->assertHtml($expected, $result);
    }

    public function testSubmitDefault()
    {
        $result = $this->CroogoForm->submit('Send');

        $expected = [
            'div' => [
                'class' => 'submit',
            ],
            [
                'input' => [
                    'class' => 'btn',
                    'type' => 'submit',
                    'value' => 'Send'
                ]
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    public function testSubmitDanger()
    {
        $result = $this->CroogoForm->submit('Send', ['button' => 'danger']);

        $expected = [
            'div' => [
                'class' => 'submit',
            ],
            [
                'input' => [
                    'class' => 'btn btn-danger',
                    'type' => 'submit',
                    'value' => 'Send'
                ]
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    public function testInputPlaceholders()
    {
        $result = $this->CroogoForm->input('username', [
            'placeholder' => true,
        ]);
        $expected = [
            'div' => [
                'class',
            ],
            'label' => ['for' => 'username'],
            'Username',
            '/label',
            'input' => [
                'name',
                'placeholder' => 'Username',
                'data-placement' => 'right',
                'data-trigger' => 'focus',
                'data-title' => 'Username',
                'type' => 'text',
                'id',
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->CroogoForm->input('username', [
            'placeholder' => 'User/Email',
        ]);
        $expected = [
            'div' => [
                'class',
            ],
            'label' => ['for' => 'username'],
            'Username',
            '/label',
            'input' => [
                'name',
                'placeholder' => 'User/Email',
                'data-placement' => 'right',
                'data-trigger' => 'focus',
                'data-title' => 'User/Email',
                'type' => 'text',
                'id',
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $tip = 'Enter your username or email address';
        $result = $this->CroogoForm->input('username', [
            'placeholder' => 'User/Email',
            'tooltip' => $tip,
        ]);
        $expected = [
            'div' => [
                'class',
            ],
            'label' => ['for' => 'username'],
            'Username',
            '/label',
            'input' => [
                'name',
                'placeholder' => 'User/Email',
                'data-placement' => 'right',
                'data-trigger' => 'focus',
                'data-title' => $tip,
                'type' => 'text',
                'id',
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->CroogoForm->input('username', [
            'placeholder' => false,
            'tooltip' => $tip,
        ]);
        $expected = [
            'div' => [
                'class',
            ],
            'label' => ['for' => 'username'],
            'Username',
            '/label',
            'input' => [
                'name',
                'data-placement' => 'right',
                'data-trigger' => 'focus',
                'data-title' => $tip,
                'type' => 'text',
                'id',
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

/**
 * testAutocomplete
 */
    public function testAutocomplete()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $result = $this->CroogoForm->autocomplete('user_id', [
            'autocomplete' => [
                'data-relatedField' => '#user_id',
                'data-url' => 'http://croogo.org',
            ],
        ]);
        $expected = [
            [
                'input' => [
                    'type' => 'hidden',
                    'name',
                    'id',
                ],
            ],
            'div' => [
                'class',
            ],
            'label' => ['for'],
            'User Id',
            '/label',
            [
                'input' => [
                    'name' => 'data[autocomplete_user_id]',
                    'data-relatedField' => '#user_id',
                    'data-url' => 'http://croogo.org',
                    'class' => 'typeahead-autocomplete',
                    'autocomplete' => 'off',
                    'type' => 'text',
                    'id'
                ],
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

/**
 * testAutocompleteWithDefault
 */
    public function testAutocompleteWithDefault()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->skipIf(env('TRAVIS') == 'true');
        $result = $this->CroogoForm->autocomplete('user_id', [
            'default' => 3,
            'autocomplete' => [
                'default' => 'yvonne',
                'data-relatedField' => '#user_id',
                'data-url' => 'http://croogo.org',
            ],
        ]);
        $expected = [
            [
                'input' => [
                    'type' => 'hidden',
                    'name',
                    'value' => 3,
                    'id',
                ],
            ],
            'div' => [
                'class',
            ],
            'label' => ['for'],
            'User Id',
            '/label',
            [
                'input' => [
                    'name' => 'data[autocomplete_user_id]',
                    'data-relatedField' => '#user_id',
                    'data-url' => 'http://croogo.org',
                    'class' => 'typeahead-autocomplete',
                    'autocomplete' => 'off',
                    'type' => 'text',
                    'value' => 'yvonne',
                    'id'
                ],
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

/**
 * testAutocompleteWithDefaultFromViewVars
 */
    public function testAutocompleteWithDefaultFromViewVars()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->skipIf(env('TRAVIS') == 'true');
        $this->CroogoForm->defaultModel = 'Node';
        $this->View->set('users', [
            3 => 'yvonne',
        ]);
        $this->View->request->data = [
            'Node' => [
                'id' => 10,
                'user_id' => 3,
            ],
        ];
        $result = $this->CroogoForm->autocomplete('Node.user_id', [
            'autocomplete' => [
                'data-relatedField' => '#NodeUserId',
                'data-displayField' => 'username',
                'data-url' => 'http://croogo.org',
            ],
        ]);
        $expected = [
            [
                'input' => [
                    'type' => 'hidden',
                    'name',
                    'value' => 3,
                    'id',
                ],
            ],
            'div' => [
                'class',
            ],
            'label' => ['for'],
            'User Id',
            '/label',
            [
                'input' => [
                    'name' => 'data[autocomplete_user_id]',
                    'value' => 'yvonne',
                    'data-displayField' => 'username',
                    'data-url' => 'http://croogo.org',
                    'data-relatedField',
                    'class',
                    'autocomplete',
                    'type',
                    'id',
                ],
            ],
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

/**
 * Test placeholder with nested model fields
 */
    public function testInputPlaceholderNestedModel()
    {
        $expected = [
            'div' => [
                'class',
            ],
            'label' => [
                'for',
            ],
            'Node',
            '/label',
            'select' => [
                'name',
                'placeholder' => 'Node',
                'data-placement',
                'data-trigger',
                'data-title',
                'id',
            ],
            '/select',
            '/div',
        ];
        $result = $this->CroogoForm->input('User.Comment.node_id', [
            'placeholder' => true,
        ]);
        $this->assertHtml($expected, $result);
    }

/**
 * Test radio button class
 */
    public function testInputRadioButtonClass()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $result = $this->CroogoForm->input('Node.promote', [
            'type' => 'radio',
            'class' => 'super-radio-button',
            'options' => [
                0 => 'Not promoted',
                1 => 'Promoted',
            ],
        ]);
        $this->assertStringStartsWith('<div class="input radio">', $result);
        $this->assertContains('class="super-radio-button"', $result);
    }

/**
 * Test checkbox class
 */
    public function testInputCheckboxClass()
    {
        $result = $this->CroogoForm->input('Node.promote', [
            'type' => 'checkbox',
            'class' => 'super-checkbox-button',
        ]);
        $this->assertStringStartsWith('<div class="input checkbox">', $result);
        $this->assertContains('class="super-checkbox-button"', $result);
    }
}
