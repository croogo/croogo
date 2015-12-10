<?php
namespace Croogo\Blocks\Test\TestCase\View\Helper;

use App\Controller\Component\SessionComponent;
use Blocks\View\Helper\RegionsHelper;
use Cake\Controller\Controller;
use Croogo\TestSuite\CroogoTestCase;
use Croogo\View\Helper\LayoutHelper;

class TheRegionsTestController extends Controller
{

    public $components = [];

    public $uses = null;
}

class RegionsHelperTest extends CroogoTestCase
{

    public $fixtures = [
        'plugin.settings.setting',
    ];

/**
 * setUp
 */
    public function setUp()
    {
        parent::setUp();
        $this->ComponentRegistry = new ComponentRegistry();

        $request = new Request('nodes/nodes/index');
        $request->params = [
            'plugin' => 'nodes',
            'controller' => 'nodes',
            'action' => 'index',
            'named' => [],
        ];
        $controller = new TheRegionsTestController($request, new Response());
        $this->View = $this->getMock(
            'View',
            ['element', 'elementExists'],
            [$controller]
        );
        $this->View->loadHelper('Croogo.Layout');
        $this->Regions = $this->getMock('RegionsHelper', ['log'], [$this->View]);
        $this->_appEncoding = Configure::read('App.encoding');
        $this->_asset = Configure::read('Asset');
        $this->_debug = Configure::read('debug');
    }

/**
 * tearDown
 */
    public function tearDown()
    {
        Configure::write('App.encoding', $this->_appEncoding);
        Configure::write('Asset', $this->_asset);
        Configure::write('debug', $this->_debug);
        ClassRegistry::flush();
        unset($this->Regions);
    }

/**
 * testIsEmpty
 */
    public function testIsEmpty()
    {
        $this->assertTrue($this->Regions->isEmpty('right'));
        $this->Regions->_View->viewVars['blocks_for_layout'] = [
            'right' => [
                '0' => ['block here'],
                '1' => ['block here'],
                '2' => ['block here'],
            ],
        ];
        $this->assertFalse($this->Regions->isEmpty('right'));
    }
    
/**
 * testBlock
 */
    public function testBlock()
    {
        $blocksForLayout = [
            'right' => [
                0 => [
                    'Block' => [
                        'id' => 1,
                        'alias' => 'hello-world',
                        'body' => 'hello world',
                        'show_title' => false,
                        'class' => null,
                        'element' => null,
                    ]
                ],
            ],
        ];
        $this->Regions->_View->viewVars['blocks_for_layout'] = $blocksForLayout;
        $this->Regions
            ->expects($this->never())
            ->method('log');
        $this->View
            ->expects($this->once())->method('element')
            ->with(
                'Blocks.block',
                ['block' => $blocksForLayout['right'][0]]
            );
        $result = $this->Regions->block('hello-world');
    }

/**
 * testBlockOptions
 */
    public function testBlockOptions()
    {
        $blocksForLayout = [
            'right' => [
                0 => [
                    'Block' => [
                        'id' => 1,
                        'alias' => 'hello-world',
                        'body' => 'hello world',
                        'show_title' => false,
                        'class' => null,
                        'element' => null,
                    ],
                    'Params' => [
                        'enclosure' => false,
                    ],
                ],
            ],
        ];
        $this->Regions->_View->viewVars['blocks_for_layout'] = $blocksForLayout;
        $this->View
            ->expects($this->once())
            ->method('elementExists')
            ->will($this->returnValue(false));

        $this->View
            ->expects($this->once())->method('element')
            ->with(
                'Blocks.block',
                ['block' => $blocksForLayout['right'][0]],
                ['class' => 'some-class', 'ignoreMissing' => true]
            );

        $result = $this->Regions->block('hello-world', [
            'elementOptions' => ['class' => 'some-class']
        ]);
    }


/**
 * testBlock with invalid/missing element
 */
    public function testBlockWithInvalidElement()
    {
        $blocksForLayout = [
            'right' => [
                0 => [
                    'Block' => [
                        'id' => 1,
                        'alias' => 'hello-world',
                        'body' => 'hello world',
                        'show_title' => false,
                        'class' => null,
                        'element' => 'non-existent',
                    ]
                ],
            ],
        ];
        $this->Regions->_View->viewVars['blocks_for_layout'] = $blocksForLayout;
        $this->Regions
            ->expects($this->once())
            ->method('log')
            ->with('Missing element `non-existent` in block `hello-world` (1)');
        $this->View
            ->expects($this->once())
            ->method('element')
            ->with('Blocks.block', ['block' => $blocksForLayout['right'][0]]);
        $result = $this->Regions->block('hello-world');
    }

/**
 * testBlocks
 */
    public function testBlocks()
    {
        $blocksForLayout = [
            'right' => [
                0 => [
                    'Block' => [
                        'id' => 1,
                        'alias' => 'hello-world',
                        'body' => 'hello world',
                        'show_title' => false,
                        'class' => null,
                        'element' => null,
                    ]
                ],
            ],
        ];
        $this->Regions->_View->viewVars['blocks_for_layout'] = $blocksForLayout;
        $this->Regions
            ->expects($this->never())
            ->method('log');
        $this->View
            ->expects($this->once())
            ->method('element')
            ->with(
                'Blocks.block',
                ['block' => $blocksForLayout['right'][0]]
            );
        $result = $this->Regions->blocks('right');
    }

/**
 * testBlocksOptions
 */
    public function testBlocksOptions()
    {
        $blocksForLayout = [
            'right' => [
                0 => [
                    'Block' => [
                        'id' => 1,
                        'alias' => 'hello-world',
                        'body' => 'hello world',
                        'show_title' => false,
                        'class' => null,
                        'element' => null,
                    ],
                    'Params' => [
                        'enclosure' => false,
                    ],
                ],
            ],
        ];
        $this->Regions->_View->viewVars['blocks_for_layout'] = $blocksForLayout;
        $this->View->expects($this->once())
            ->method('elementExists')
            ->will($this->returnValue(false));

        $this->View->expects($this->once())
            ->method('element')
            ->with(
                'Blocks.block',
                ['block' => $blocksForLayout['right'][0]],
                ['class' => 'some-class', 'ignoreMissing' => true]
            );

        $result = $this->Regions->blocks('right', [
            'elementOptions' => ['class' => 'some-class']
        ]);
    }

/**
 * testBlocks with invalid/missing element
 */
    public function testBlocksWithInvalidElement()
    {
        $blocksForLayout = [
            'right' => [
                0 => [
                    'Block' => [
                        'id' => 1,
                        'alias' => 'hello-world',
                        'body' => 'hello world',
                        'show_title' => false,
                        'class' => null,
                        'element' => 'non-existent',
                    ]
                ],
            ],
        ];
        $this->Regions->_View->viewVars['blocks_for_layout'] = $blocksForLayout;
        $this->Regions
            ->expects($this->once())
            ->method('log')
            ->with('Missing element `non-existent` in block `hello-world` (1)');
        $this->View
            ->expects($this->once())
            ->method('element')
            ->with('Blocks.block', ['block' => $blocksForLayout['right'][0]]);
        $result = $this->Regions->blocks('right');
    }
}
