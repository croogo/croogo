<?php

namespace Croogo\Core\Test\TestCase\Utility;

use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\ORM\Entity;
use Cake\Utility\Hash;
use Croogo\Core\TestSuite\CroogoTestCase;
use Croogo\Core\Utility\VisibilityFilter;

class VisibilityFilterTest extends CroogoTestCase
{

    public $setupSettings = false;

    protected function _testData()
    {
        return collection([
            new Entity([
                'id' => 1,
                'visibility_paths' => [
                    'plugin:nodes',
                    '-plugin:contacts/controller:contacts/action:view',
                ],
            ]),
            new Entity([
                'id' => 2,
                'visibility_paths' => [
                    'plugin:nodes/controller:nodes/action:promoted',
                    'plugin:contacts/controller:contacts/action:view',
                ],
            ]),
            new Entity([
                'id' => 3,
                'visibility_paths' => [
                    '-plugin:nodes/controller:nodes/action:promoted',
                    '-plugin:contacts/controller:contacts/action:view/contact',
                ],
            ]),
            new Entity([
                'id' => 4,
                'visibility_paths' => []
            ]),
            new Entity([
                'id' => 5,
                'visibility_paths' => [
                    'plugin:nodes/controller:bogus_nodes',
                    'plugin:contacts/controller:contacts',
                ],
            ]),
            new Entity([
                'id' => 6,
                'visibility_paths' => [
                    'plugin:nodes/controller:nodes/action:index/type:blog?page=8',
                ],
            ]),
        ]);
    }

    public function testLinkstringRule()
    {
        $request = new Request();
        $request->addParams([
            'controller' => 'nodes',
            'plugin' => 'nodes',
            'action' => 'promoted',
        ]);
        $Filter = new VisibilityFilter($request);
        $blocks = $this->_testData();
        $results = $Filter->remove($blocks, [
            'field' => 'visibility_paths',
        ]);

        $this->assertTrue(!$results->match([
            'id' => 1
        ])->isEmpty(), 'partial match');

        $this->assertTrue(!$results->match([
            'id' => 2
        ])->isEmpty(), 'exact match');

        $this->assertFalse(!$results->match([
            'id' => 3
        ])->isEmpty(), 'negation');

        $this->assertTrue(!$results->match([
            'id' => 4
        ])->isEmpty(), 'empty rule');

        $this->assertFalse(!$results->match([
            'id' => 5
        ])->isEmpty(), 'same plugin, different controller');

        $this->assertFalse(!$results->match([
            'id' => 6
        ])->isEmpty(), 'with query string');
    }

    public function testLinkstringRuleWithContacts()
    {
        $request = new Request();
        $request->addParams([
            'controller' => 'contacts',
            'plugin' => 'contacts',
            'action' => 'view',
        ]);
        $Filter = new VisibilityFilter($request);
        $blocks = $this->_testData();
        $results = $Filter->remove($blocks, [
            'field' => 'visibility_paths',
        ]);

        $this->assertTrue(!$results->match([
            'id' => 2
        ])->isEmpty(), 'exact match');

        $this->assertTrue(!$results->match([
            'id' => 3
        ])->isEmpty(), 'negation rule with passedArgs');

        $this->assertTrue(!$results->match([
            'id' => 4
        ])->isEmpty(), 'empty rule');

        $this->assertTrue(!$results->match([
            'id' => 5
        ])->isEmpty(), 'partial rule');

        $this->assertFalse(!$results->match([
            'id' => 6
        ])->isEmpty(), 'with query string');
    }

    public function testLinkstringRuleWithQueryString()
    {
        $request = new Request();
        $request->addParams([
            'controller' => 'nodes',
            'plugin' => 'nodes',
            'action' => 'index',
            'type' => 'blog',
        ]);
        $request->query = [
            'page' => '8',
        ];
        $Filter = new VisibilityFilter($request);
        $blocks = $this->_testData();
        Configure::write('foo', true);
        $results = $Filter->remove($blocks, [
            'field' => 'visibility_paths',
        ]);

        $this->assertTrue(!$results->match([
            'id' => 6
        ])->isEmpty(), 'exact match with query string');
    }
}
