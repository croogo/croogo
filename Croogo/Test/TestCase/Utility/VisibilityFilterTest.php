<?php

namespace Croogo\Croogo\Test\TestCase\Utility;
App::uses('CroogoTestCase', 'Croogo.TestSuite');
App::uses('VisibilityFilter', 'Croogo.Utility');

class VisibilityFilterTest extends CroogoTestCase {

	public $setupSettings = false;

	protected function _testData() {
		return array(
			array(
				'Block' => array(
					'id' => 1,
					'visibility_paths' => array(
						'plugin:nodes',
						'-plugin:contacts/controller:contacts/action:view',
					),
				),
			),
			array(
				'Block' => array(
					'id' => 2,
					'visibility_paths' => array(
						'plugin:nodes/controller:nodes/action:promoted',
						'plugin:contacts/controller:contacts/action:view',
					),
				),
			),
			array(
				'Block' => array(
					'id' => 3,
					'visibility_paths' => array(
						'-plugin:nodes/controller:nodes/action:promoted',
						'-plugin:contacts/controller:contacts/action:view/contact',
					),
				),
			),
			array(
				'Block' => array(
					'id' => 4,
					'visibility_paths' => ''
				),
			),
			array(
				'Block' => array(
					'id' => 5,
					'visibility_paths' => array(
						'plugin:nodes/controller:bogus_nodes',
						'plugin:contacts/controller:contacts',
					),
				),
			),
			array(
				'Block' => array(
					'id' => 6,
					'visibility_paths' => array(
						'plugin:nodes/controller:nodes/action:index/type:blog?page=8',
					),
				),
			),
		);
	}

	public function testLinkstringRule() {
		$request = new CakeRequest();
		$request->addParams(array(
			'controller' => 'nodes',
			'plugin' => 'nodes',
			'action' => 'promoted',
		));
		$Filter = new VisibilityFilter($request);
		$blocks = $this->_testData();
		$results = $Filter->remove($blocks, array(
			'model' => 'Block',
			'field' => 'visibility_paths',
		));

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

	public function testLinkstringRuleWithContacts() {
		$request = new CakeRequest();
		$request->addParams(array(
			'controller' => 'contacts',
			'plugin' => 'contacts',
			'action' => 'view',
		));
		$Filter = new VisibilityFilter($request);
		$blocks = $this->_testData();
		$results = $Filter->remove($blocks, array(
			'model' => 'Block',
			'field' => 'visibility_paths',
		));

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

	public function testLinkstringRuleWithQueryString() {
		$request = new CakeRequest();
		$request->addParams(array(
			'controller' => 'nodes',
			'plugin' => 'nodes',
			'action' => 'index',
			'type' => 'blog',
		));
		$request->query = array(
			'page' => '8',
		);
		$Filter = new VisibilityFilter($request);
		$blocks = $this->_testData();
		Configure::write('foo', true);
		$results = $Filter->remove($blocks, array(
			'model' => 'Block',
			'field' => 'visibility_paths',
		));

		// exact match with query string
		$this->assertTrue(Hash::check($results, '{n}.Block[id=6]'));
	}

}
