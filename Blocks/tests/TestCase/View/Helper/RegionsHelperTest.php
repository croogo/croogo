<?php
namespace Croogo\Blocks\Test\TestCase\View\Helper;

use Cake\ORM\TableRegistry;
use Cake\View\View;
use Croogo\Core\TestSuite\TestCase;

class RegionsHelperTest extends TestCase
{

    public $fixtures = [
        'plugin.croogo/blocks.block',
    ];

    /**
     * @var \Cake\View\View
     */
    public $view;

    /**
     * @var \Croogo\Blocks\View\Helper\RegionsHelper
     */
    public $helper;

    /**
 * setUp
 */
    public function setUp()
    {
        parent::setUp();

        $this->view = $this->getMock('Cake\View\View', [
            'element',
            'elementExists'
        ]);

        $this->helper = $this->getMock('Croogo\Blocks\View\Helper\RegionsHelper', [
            'log'
        ], [
            $this->view
        ]);
    }

/**
 * testIsEmpty
 */
    public function testIsEmpty()
    {
        $this->assertTrue($this->helper->isEmpty('right'));
        $this->view->viewVars['blocksForLayout'] = [
            'right' => [
                '0' => ['block here'],
                '1' => ['block here'],
                '2' => ['block here'],
            ],
        ];
        $this->assertFalse($this->helper->isEmpty('right'));
    }

    public function testBlock()
    {
        $search = TableRegistry::get('Croogo/Blocks.Blocks')->findByAlias('search')->first();

        $this->view
            ->expects($this->once())->method('element')
            ->with(
                'Croogo/Blocks.block',
                ['block' => $search]
            );

        $this->helper->block($search);
    }

    public function testBlockOptions()
    {
        $search = TableRegistry::get('Croogo/Blocks.Blocks')->findByAlias('search')->first();

        $this->view
            ->expects($this->once())
            ->method('elementExists')
            ->will($this->returnValue(false));

        $this->view
            ->expects($this->once())
            ->method('element')
            ->with(
                'Croogo/Blocks.block',
                ['block' => $search],
                ['class' => 'some-class', 'ignoreMissing' => true]
            );

        $this->helper->block($search, 'right', [
            'elementOptions' => ['class' => 'some-class']
        ]);
    }


    /**
     * testBlock with invalid/missing element
     */
    public function testBlockWithInvalidElement()
    {
        $search = TableRegistry::get('Croogo/Blocks.Blocks')->findByAlias('search')->first();

        $blocksForLayout = [
            'right' => [
                $search,
            ],
        ];
        $this->view->viewVars['blocksForLayout'] = $blocksForLayout;
        $this->helper
            ->expects($this->once())
            ->method('log')
            ->with('Missing element `Nodes.search` in block `search` (8)');
        $this->view
            ->expects($this->once())
            ->method('element')
            ->with('Croogo/Blocks.block', ['block' => $search]);
        $result = $this->helper->block($search);
    }

    public function testBlocks()
    {
        $search = TableRegistry::get('Croogo/Blocks.Blocks')->findByAlias('search')->first();

        $blocksForLayout = [
            'right' => [
                $search,
            ],
        ];
        $this->view->viewVars['blocksForLayout'] = $blocksForLayout;
        $this->view
            ->expects($this->once())
            ->method('element')
            ->with(
                'Croogo/Blocks.block',
                ['block' => $search]
            );
        $this->helper->blocks('right');
    }

    public function testBlocksOptions()
    {
        $search = TableRegistry::get('Croogo/Blocks.Blocks')->findByAlias('search')->first();
        $search->params = [
            'enclosure' => true
        ];

        $blocksForLayout = [
            'right' => [
                $search,
            ],
        ];
        $this->view->viewVars['blocksForLayout'] = $blocksForLayout;
        $this->view->expects($this->once())
            ->method('elementExists')
            ->will($this->returnValue(false));

        $this->view->expects($this->once())
            ->method('element')
            ->with(
                'Croogo/Blocks.block',
                ['block' => $search],
                ['class' => 'some-class', 'ignoreMissing' => true]
            );

        $this->helper->blocks('right', [
            'elementOptions' => ['class' => 'some-class']
        ]);
    }

    /**
     * testBlocks with invalid/missing element
     */
    public function testBlocksWithInvalidElement()
    {
        $search = TableRegistry::get('Croogo/Blocks.Blocks')->findByAlias('search')->first();

        $blocksForLayout = [
            'right' => [
                $search
            ],
        ];
        $this->view->viewVars['blocksForLayout'] = $blocksForLayout;
        $this->helper
            ->expects($this->once())
            ->method('log')
            ->with('Missing element `Nodes.search` in block `search` (8)');
        $this->view
            ->expects($this->once())
            ->method('element')
            ->with('Croogo/Blocks.block', ['block' => $search]);
        $result = $this->helper->blocks('right');
    }
}
