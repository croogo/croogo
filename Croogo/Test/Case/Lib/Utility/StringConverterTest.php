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
 * testLinkStringToArrayWithQueryString
 */
	public function testLinkStringToArrayWithQueryString() {
		$expected = array(
			'admin' => true,
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'index',
			'?' => array(
				'foo' => 'bar',
			),
		);
		$result = $this->Converter->linkStringToArray(
			'admin:true/plugin:nodes/controller:nodes/action:index?foo=bar'
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testLinkStringToArrayWithQueryStringAndPassedArgs
 */
	public function testLinkStringToArrayWithQueryStringAndPassedArgs() {
		$expected = array(
			'admin' => true,
			'plugin' => 'settings',
			'controller' => 'settings',
			'action' => 'prefix',
			'Site',
			'?' => array(
				'key' => 'Site.title',
			),
		);
		$result = $this->Converter->linkStringToArray(
			'admin:true/plugin:settings/controller:settings/action:prefix/Site?key=Site.title'
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testLinkStringToArrayWithQueryStringAndPassedAndNamedArgs
 */
	public function testLinkStringToArrayWithQueryStringAndPassedAndNamedArgs() {
		$expected = array(
			'admin' => false,
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'index',
			'type' => 'blog',
			'?' => array(
				'slug' => 'hello-world',
			),
		);
		$result = $this->Converter->linkStringToArray(
			'admin:false/plugin:nodes/controller:nodes/action:index/type:blog?slug=hello-world'
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testLinkStringToArrayWithUtf8
 */
	public function testLinkStringToArrayWithUtf8() {
		$expected = array(
			'admin' => false,
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'view',
			'type' => 'blog',
			'slug' => 'ハローワールド',
		);
		$result = $this->Converter->linkStringToArray(
			'admin:false/plugin:nodes/controller:nodes/action:view/type:blog/slug:ハローワールド'
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testLinkStringToArrayWithUtf8PassedArgs
 */
	public function testLinkStringToArrayWithUtf8PassedArgs() {
		$expected = array(
			'admin' => false,
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'view',
			'ハローワールド',
			'좋은 아침',
		);
		$result = $this->Converter->linkStringToArray(
			'admin:false/plugin:nodes/controller:nodes/action:view/ハローワールド/좋은 아침'
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testLinkStringToArrayWithUtf8InQueryString
 */
	public function testLinkStringToArrayWithUtf8InQueryString() {
		$expected = array(
			'admin' => false,
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'view',
			'?' => array(
				'slug' => 'ハローワールド',
				'page' => '8',
			),
		);
		$result = $this->Converter->linkStringToArray(
			'admin:false/plugin:nodes/controller:nodes/action:view/?slug=ハローワールド&page=8'
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testLinkStringToArrayWithEncodedUtf8
 */
	public function testLinkStringToArrayWithEncodedUtf8() {
		$expected = array(
			'admin' => false,
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'view',
			'type' => 'blog',
			'slug' => 'ハローワールド',
		);
		$result = $this->Converter->linkStringToArray(
			'admin:false/plugin:nodes/controller:nodes/action:view/type:blog/slug:%E3%83%8F%E3%83%AD%E3%83%BC%E3%83%AF%E3%83%BC%E3%83%AB%E3%83%89'
		);
		$this->assertEquals($expected, $result);
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

	public function testUrlToLinkStringWithQueryStringAndNamedArgs() {

		$url = array(
			'controller' => 'contacts',
			'action' => 'view',
			'plugin' => 'contacts',
			'?' => array(
				'slug' => 'contact',
				'page' => '8',
			),
		);
		$expected = 'plugin:contacts/controller:contacts/action:view?slug=contact&page=8';
		$this->assertEquals($expected, $this->Converter->urlToLinkString($url));

		$url = array(
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'term',
			'type' => 'page',
			'?' => array(
				'slug' => 'uncategorized',
			),
		);
		$expected = 'plugin:nodes/controller:nodes/action:term/type:page?slug=uncategorized';
		$this->assertEquals($expected, $this->Converter->urlToLinkString($url));
	}

/**
 * testFirstPara
 */
	public function testFirstPara() {
		$text = '<p>First paragraph</p>';
		$expected = 'First paragraph';
		$result = $this->Converter->firstPara($text);
		$this->assertEquals($expected, $result);

		$text = '<p class="foo"><span style="font-size: 100%">First<span> paragraph</p>';
		$expected = 'First paragraph';
		$result = $this->Converter->firstPara($text);
		$this->assertEquals($expected, $result);

		$expected = '<p>First paragraph</p>';
		$result = $this->Converter->firstPara($text, array('tag' => true));
		$this->assertEquals($expected, $result);

		$text = '<p class="foo"><span style="font-size: 100%">First<span> paragraph</p>';
		$expected = '<p><span style="font-size: 100%">First<span> paragraph</p>';
		$result = $this->Converter->firstPara($text, array('tag' => true, 'stripTags' => false));
		$this->assertEquals($expected, $result);

		$text = "This is the first paragraph.  And this is the second sentence.
This should be the second paragraph.  And this is the fourth sentence, located in the second paragraph";
		$expected = 'This is the first paragraph.  And this is the second sentence.';
		$result = $this->Converter->firstPara($text, array('newline' => true));
		$this->assertEquals($expected, $result);

		$text = "This is the first paragraph.\nThis should be the second paragraph.";
		$expected = '<p>This is the first paragraph.</p>';
		$result = $this->Converter->firstPara($text, array('tag' => true, 'newline' => true));
		$this->assertEquals($expected, $result);
	}

}
