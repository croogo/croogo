<?php

App::uses('CroogoTestCase', 'Croogo.TestSuite');
App::uses('StringConverter', 'Croogo.Utility');

class StringConverterTest extends CroogoTestCase {

	public $setupSettings = false;

	public function setUp() {
		parent::setUp();
		$this->Converter = new StringConverter();
	}

/**
 * testLinkStringToArray
 */
	public function testLinkStringToArray() {
		$this->assertEqual($this->Converter->linkStringToArray('controller:nodes/action:index'), array_merge(
			array_fill_keys((array)Configure::read('Routing.prefixes'), false),
			array(
				'plugin' => null,
				'controller' => 'nodes',
				'action' => 'index',
			)
		));
		$this->assertEqual($this->Converter->linkStringToArray('controller:nodes/action:index/pass/pass2'), array_merge(
			array_fill_keys((array)Configure::read('Routing.prefixes'), false),
			array(
				'plugin' => null,
				'controller' => 'nodes',
				'action' => 'index',
				'pass',
				'pass2',
			)
		));
		$this->assertEqual($this->Converter->linkStringToArray('controller:nodes/action:index/param:value'), array_merge(
			array_fill_keys((array)Configure::read('Routing.prefixes'), false),
			array(
				'plugin' => null,
				'controller' => 'nodes',
				'action' => 'index',
				'param' => 'value',
			)
		));
		$this->assertEqual($this->Converter->linkStringToArray('controller:nodes/action:index/with-slash/'), array_merge(
			array_fill_keys((array)Configure::read('Routing.prefixes'), false),
			array(
				'plugin' => null,
				'controller' => 'nodes',
				'action' => 'index',
				'with-slash',
			)
		));

		$expected = array_merge(
			array_fill_keys((array)Configure::read('Routing.prefixes'), false),
			array(
				'plugin' => 'contacts',
				'controller' => 'contacts',
				'action' => 'view',
				'contact',
			)
		);
		$string = 'plugin:contacts/controller:contacts/action:view/contact';
		$this->assertEqual($expected, $this->Converter->linkStringToArray($string));

		$string = '/plugin:contacts/controller:contacts/action:view/contact';
		$this->assertEqual($expected, $this->Converter->linkStringToArray($string));
	}

/**
 * testUrlToLinkString
 */
	public function testUrlToLinkString() {
		$url = array(
			'controller' => 'contacts',
			'action' => 'view',
			'contact',
			'plugin' => 'contacts',
		);
		$expected = 'plugin:contacts/controller:contacts/action:view/contact';
		$this->assertEquals($expected, $this->Converter->urlToLinkString($url));

		$url = array(
			'plugin' => 'contacts',
			'controller' => 'contacts',
			'action' => 'view',
			'contact',
		);
		$expected = 'plugin:contacts/controller:contacts/action:view/contact';
		$this->assertEquals($expected, $this->Converter->urlToLinkString($url));

		$url = array(
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'view',
			'type' => 'blog',
			'hello'
		);
		$expected = 'plugin:nodes/controller:nodes/action:view/type:blog/hello';
		$this->assertEquals($expected, $this->Converter->urlToLinkString($url));

		$url = array(
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'view',
			'live',
			'long',
			'and',
			'prosper',
		);
		$expected = 'plugin:nodes/controller:nodes/action:view/live/long/and/prosper';
		$this->assertEquals($expected, $this->Converter->urlToLinkString($url));

		$url = array(
			'controller' => 'nodes',
			'action' => 'view',
			'live',
			'long',
			'and',
			'prosper',
		);
		$expected = 'controller:nodes/action:view/live/long/and/prosper';
		$this->assertEquals($expected, $this->Converter->urlToLinkString($url));

		$url = array(
			'admin' => true,
			'controller' => 'nodes',
			'action' => 'edit',
			1,
			'type' => 'blog',
		);
		$expected = 'admin/controller:nodes/action:edit/1/type:blog';
		$this->assertEquals($expected, $this->Converter->urlToLinkString($url));

		$url = array();
		$this->assertEquals('', $this->Converter->urlToLinkString($url));

		$url = array('some' => 'random', 1, 2, 'array' => 'must', 'work');
		$expected = 'some:random/1/2/array:must/work';
		$this->assertEquals($expected, $this->Converter->urlToLinkString($url));
	}

}
