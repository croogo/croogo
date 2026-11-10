<?php

namespace Croogo\Core\Test\TestCase\View\Helper;

use Cake\Http\ServerRequest as Request;
use Cake\Http\Response;
use Cake\View\View;
use Croogo\Core\TestSuite\CroogoTestCase;
use Croogo\Core\View\Helper\HtmlHelper;

class HtmlHelperTest extends CroogoTestCase
{

    protected array $fixtures = [
//      'plugin.Croogo/Taxonomy.Type',
    ];

    /**
     * @var Croogo\Core\View\Helper\HtmlHelper
     */
    private $Html;

    public function setUp(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $controller = null;
        $this->View = new View(new Request, new Response);
        $this->Html = new HtmlHelper($this->View);
    }

    public function tearDown(): void
    {
        unset($this->View);
        unset($this->Html);
    }

    public function testIcon(): void
    {
        $result = $this->Html->icon('remove');
        $this->assertContains('<i class="icon-remove"></i>', $result);
    }

    public function testStatusOk(): void
    {
        $result = $this->Html->status(1);
        $this->assertContains('<i class="icon-ok green"></i>', $result);
    }

    public function testStatusOkWithUrl(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $result = $this->Html->status(1, [
            'prefix' => 'admin',
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'toggle',
        ]);
        $expected = [
            'a' => [
                'href',
                'data-url' => '/admin/nodes/nodes/toggle',
                'class' => 'icon-ok green ajax-toggle',
            ],
            '/a',
        ];
        $this->assertHtml($expected, $result);
    }

    public function testStatusRemove(): void
    {
        $result = $this->Html->status(0);
        $this->assertContains('<i class="icon-remove red"></i>', $result);
    }

    public function testStatusRemoveWithUrl(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $result = $this->Html->status(0, [
            'prefix' => 'admin',
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'delete',
        ]);
        $expected = [
            'a' => [
                'href',
                'data-url' => '/admin/nodes/nodes/delete',
                'class' => 'icon-remove red ajax-toggle',
            ],
            '/a',
        ];
        $this->assertHtml($expected, $result);
    }

    public function testLink(): void
    {
        $result = $this->Html->link('', '/remove', ['icon' => 'remove', 'button' => 'danger']);
        $this->assertContains('class="btn btn-danger"', $result);
        $this->assertContains('<i class="icon-remove icon-large"></i>', $result);
    }

    /**
     * testLinkWithSmallIcon
     */
    public function testLinkWithSmallIcon(): void
    {
        $result = $this->Html->link('', '/remove', [
            'icon' => 'remove',
            'iconSize' => 'small',
            'button' => 'danger'
        ]);
        $this->assertContains('class="btn btn-danger"', $result);
        $this->assertContains('<i class="icon-remove"></i>', $result);
    }

    /**
     * testLinkWithInlineIcon
     */
    public function testLinkWithInlineIcon(): void
    {
        $result = $this->Html->link('', '/remove', [
            'icon' => 'remove',
            'iconSize' => 'small',
            'iconInline' => true,
            'button' => 'danger'
        ]);
        $expected = [
            'a' => [
                'href',
                'class' => 'btn btn-danger icon-remove',
            ],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->link('', '/remove', [
            'icon' => 'remove',
            'iconInline' => true,
            'button' => 'danger'
        ]);
        $expected = [
            'a' => [
                'href',
                'class' => 'btn btn-danger icon-large icon-remove',
            ],
        ];
        $this->assertHtml($expected, $result);
    }

    public function testLinkDefaultButton(): void
    {
        $result = $this->Html->link('Remove', '/remove', ['button' => 'default']);
        $this->assertContains('<a href="/remove" class="btn btn-default">Remove</a>', $result);
    }

    public function testLinkOptionsIsNull(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $result = $this->Html->link('Remove', '/remove', null);
    }

    public function testLinkTooltip(): void
    {
        $result = $this->Html->link('', '/remove', ['tooltip' => 'remove it']);
        $expected = [
            'a' => [
                'href',
                'rel' => 'tooltip',
                'data-placement',
                'data-trigger',
                'data-title' => 'remove it',
            ],
            '/a',
        ];
        $this->assertHtml($expected, $result);
    }

    public function testLinkButtonTooltipWithArrayOptions(): void
    {
        $result = $this->Html->link('', '/remove', [
            'button' => ['success'],
            'tooltip' => [
                'data-title' => 'remove it',
                'data-placement' => 'left',
                'data-trigger' => 'focus',
            ],
        ]);
        $expected = [
            'a' => [
                'href',
                'class' => 'btn btn-success',
                'rel' => 'tooltip',
                'data-placement' => 'left',
                'data-trigger' => 'focus',
                'data-title' => 'remove it',
            ],
            '/a',
        ];
        $this->assertHtml($expected, $result);
    }

    public function testAddPathAndGetCrumbList(): void
    {
        $this->Html->addPath('/yes/we/can', '/');
        $result = $this->Html->getCrumbList();
        $this->assertContains('<a href="/yes/">yes</a>', $result);
        $this->assertContains('<a href="/yes/we/">we</a>', $result);
        $this->assertContains('<a href="/yes/we/can/">can</a>', $result);
    }
}
