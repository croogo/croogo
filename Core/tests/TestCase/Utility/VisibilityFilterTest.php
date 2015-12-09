<?php

namespace Croogo\Core\Test\TestCase\Utility;

use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\Utility\Hash;
use Croogo\Core\TestSuite\CroogoTestCase;
use Croogo\Core\Utility\VisibilityFilter;

class VisibilityFilterTest extends CroogoTestCase
{

    public $setupSettings = false;

    protected function _testData()
    {
        return [
            [
                'Block' => [
                    'id' => 1,
                    'visibility_paths' => [
                        'plugin:nodes',
                        '-plugin:contacts/controller:contacts/action:view',
                    ],
                ],
            ],
            [
                'Block' => [
                    'id' => 2,
                    'visibility_paths' => [
                        'plugin:nodes/controller:nodes/action:promoted',
                        'plugin:contacts/controller:contacts/action:view',
                    ],
                ],
            ],
            [
                'Block' => [
                    'id' => 3,
                    'visibility_paths' => [
                        '-plugin:nodes/controller:nodes/action:promoted',
                        '-plugin:contacts/controller:contacts/action:view/contact',
                    ],
                ],
            ],
            [
                'Block' => [
                    'id' => 4,
                    'visibility_paths' => ''
                ],
            ],
            [
                'Block' => [
                    'id' => 5,
                    'visibility_paths' => [
                        'plugin:nodes/controller:bogus_nodes',
                        'plugin:contacts/controller:contacts',
                    ],
                ],
            ],
            [
                'Block' => [
                    'id' => 6,
                    'visibility_paths' => [
                        'plugin:nodes/controller:nodes/action:index/type:blog?page=8',
                    ],
                ],
            ],
        ];
    }

    public function testLinkstringRule()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $request = new Request();
        $request->addParams([
            'controller' => 'nodes',
            'plugin' => 'nodes',
            'action' => 'promoted',
        ]);
        $Filter = new VisibilityFilter($request);
        $blocks = $this->_testData();
        $results = $Filter->remove($blocks, [
            'model' => 'Block',
            'field' => 'visibility_paths',
        ]);

        // partial match
        $this->assertTrue(Hash::check($results, '{n}.Block[id=1]'));

        // exact match
        $this->assertTrue(Hash::check($results, '{n}.Block[id=2]'));

        // negation
        $this->assertFalse(Hash::check($results, '{n}.Block[id=3]'));

        // empty rule
        $this->assertTrue(Hash::check($results, '{n}.Block[id=4]'));

        // same plugin, different controller
        $this->assertFalse(Hash::check($results, '{n}.Block[id=5]'));

        // with query string
        $this->assertFalse(Hash::check($results, '{n}.Block[id=6]'));
    }

    public function testLinkstringRuleWithContacts()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $request = new Request();
        $request->addParams([
            'controller' => 'contacts',
            'plugin' => 'contacts',
            'action' => 'view',
        ]);
        $Filter = new VisibilityFilter($request);
        $blocks = $this->_testData();
        $results = $Filter->remove($blocks, [
            'model' => 'Block',
            'field' => 'visibility_paths',
        ]);

        // exact match
        $this->assertTrue(Hash::check($results, '{n}.Block[id=2]'));

        // negation rule with passedArgs
        $this->assertTrue(Hash::check($results, '{n}.Block[id=3]'));

        // empty rule
        $this->assertTrue(Hash::check($results, '{n}.Block[id=4]'));

        // partial rule
        $this->assertTrue(Hash::check($results, '{n}.Block[id=5]'));

        // with query string
        $this->assertFalse(Hash::check($results, '{n}.Block[id=6]'));
    }

    public function testLinkstringRuleWithQueryString()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

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
            'model' => 'Block',
            'field' => 'visibility_paths',
        ]);

        // exact match with query string
        $this->assertTrue(Hash::check($results, '{n}.Block[id=6]'));
    }
}
