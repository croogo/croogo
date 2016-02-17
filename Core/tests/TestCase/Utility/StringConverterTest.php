<?php

namespace Croogo\Core\Test\TestCase\Utility;

use Cake\Core\Configure;
use Croogo\Core\TestSuite\CroogoTestCase;
use Croogo\Core\Utility\StringConverter;

class StringConverterTest extends CroogoTestCase
{

    public $setupSettings = false;

    /**
     * @var StringConverter
     */
    private $Converter;

    public function setUp()
    {
        parent::setUp();
        $this->Converter = new StringConverter();
    }

/**
 * testLinkStringToArray
 */
    public function testLinkStringToArray()
    {
        $this->assertEquals([
            'plugin' => null,
            'controller' => 'nodes',
            'action' => 'index',
        ], $this->Converter->linkStringToArray('controller:nodes/action:index'));
        $this->assertEquals([
            'plugin' => null,
            'controller' => 'nodes',
            'action' => 'index',
            'pass',
            'pass2',
        ], $this->Converter->linkStringToArray('controller:nodes/action:index/pass/pass2'));
        $this->assertEquals([
            'plugin' => null,
            'controller' => 'nodes',
            'action' => 'index',
            'param' => 'value',
        ], $this->Converter->linkStringToArray('controller:nodes/action:index/param:value'));
        $this->assertEquals([
            'plugin' => null,
            'controller' => 'nodes',
            'action' => 'index',
            'with-slash',
        ], $this->Converter->linkStringToArray('controller:nodes/action:index/with-slash/'));

        $expected = [
            'plugin' => 'contacts',
            'controller' => 'contacts',
            'action' => 'view',
            'contact'
        ];
        $string = 'plugin:contacts/controller:contacts/action:view/contact';
        $this->assertEquals($expected, $this->Converter->linkStringToArray($string));

        $string = '/plugin:contacts/controller:contacts/action:view/contact';
        $this->assertEquals($expected, $this->Converter->linkStringToArray($string));
    }

/**
 * testLinkStringToArrayWithQueryString
 */
    public function testLinkStringToArrayWithQueryString()
    {
        $expected = [
            'prefix' => 'admin',
            'plugin' => 'nodes',
            'controller' => 'nodes',
            'action' => 'index',
            '?' => [
                'foo' => 'bar',
            ],
        ];
        $result = $this->Converter->linkStringToArray(
            'prefix:admin/plugin:nodes/controller:nodes/action:index?foo=bar'
        );
        $this->assertEquals($expected, $result);
    }

/**
 * testLinkStringToArrayWithQueryStringAndPassedArgs
 */
    public function testLinkStringToArrayWithQueryStringAndPassedArgs()
    {
        $expected = [
            'prefix' => 'admin',
            'plugin' => 'settings',
            'controller' => 'settings',
            'action' => 'prefix',
            'Site',
            '?' => [
                'key' => 'Site.title',
            ],
        ];
        $result = $this->Converter->linkStringToArray(
            'prefix:admin/plugin:settings/controller:settings/action:prefix/Site?key=Site.title'
        );
        $this->assertEquals($expected, $result);
    }

/**
 * testLinkStringToArrayWithQueryStringAndPassedAndNamedArgs
 */
    public function testLinkStringToArrayWithQueryStringAndPassedAndNamedArgs()
    {
        $expected = [
            'prefix' => false,
            'plugin' => 'nodes',
            'controller' => 'nodes',
            'action' => 'index',
            'type' => 'blog',
            '?' => [
                'slug' => 'hello-world',
            ],
        ];
        $result = $this->Converter->linkStringToArray(
            'prefix:false/plugin:nodes/controller:nodes/action:index/type:blog?slug=hello-world'
        );
        $this->assertEquals($expected, $result);
    }

/**
 * testLinkStringToArrayWithUtf8
 */
    public function testLinkStringToArrayWithUtf8()
    {
        $expected = [
            'prefix' => false,
            'plugin' => 'nodes',
            'controller' => 'nodes',
            'action' => 'view',
            'type' => 'blog',
            'slug' => 'ハローワールド',
        ];
        $result = $this->Converter->linkStringToArray(
            'prefix:false/plugin:nodes/controller:nodes/action:view/type:blog/slug:ハローワールド'
        );
        $this->assertEquals($expected, $result);
    }

/**
 * testLinkStringToArrayWithUtf8PassedArgs
 */
    public function testLinkStringToArrayWithUtf8PassedArgs()
    {
        $expected = [
            'prefix' => false,
            'plugin' => 'nodes',
            'controller' => 'nodes',
            'action' => 'view',
            'ハローワールド',
            '좋은 아침',
        ];
        $result = $this->Converter->linkStringToArray(
            'prefix:false/plugin:nodes/controller:nodes/action:view/ハローワールド/좋은 아침'
        );
        $this->assertEquals($expected, $result);
    }

/**
 * testLinkStringToArrayWithUtf8InQueryString
 */
    public function testLinkStringToArrayWithUtf8InQueryString()
    {
        $expected = [
            'prefix' => false,
            'plugin' => 'nodes',
            'controller' => 'nodes',
            'action' => 'view',
            '?' => [
                'slug' => 'ハローワールド',
                'page' => '8',
            ],
        ];
        $result = $this->Converter->linkStringToArray(
            'prefix:false/plugin:nodes/controller:nodes/action:view/?slug=ハローワールド&page=8'
        );
        $this->assertEquals($expected, $result);
    }

/**
 * testLinkStringToArrayWithEncodedUtf8
 */
    public function testLinkStringToArrayWithEncodedUtf8()
    {
        $expected = [
            'prefix' => false,
            'plugin' => 'nodes',
            'controller' => 'nodes',
            'action' => 'view',
            'type' => 'blog',
            'slug' => 'ハローワールド',
        ];
        $result = $this->Converter->linkStringToArray(
            'prefix:false/plugin:nodes/controller:nodes/action:view/type:blog/slug:%E3%83%8F%E3%83%AD%E3%83%BC%E3%83%AF%E3%83%BC%E3%83%AB%E3%83%89'
        );
        $this->assertEquals($expected, $result);
    }

/**
 * testUrlToLinkString
 */
    public function testUrlToLinkString()
    {
        $url = [
            'controller' => 'contacts',
            'action' => 'view',
            'contact',
            'plugin' => 'contacts',
        ];
        $expected = 'plugin:contacts/controller:contacts/action:view/contact';
        $this->assertEquals($expected, $this->Converter->urlToLinkString($url));

        $url = [
            'plugin' => 'contacts',
            'controller' => 'contacts',
            'action' => 'view',
            'contact',
        ];
        $expected = 'plugin:contacts/controller:contacts/action:view/contact';
        $this->assertEquals($expected, $this->Converter->urlToLinkString($url));

        $url = [
            'plugin' => 'nodes',
            'controller' => 'nodes',
            'action' => 'view',
            'type' => 'blog',
            'hello'
        ];
        $expected = 'plugin:nodes/controller:nodes/action:view/type:blog/hello';
        $this->assertEquals($expected, $this->Converter->urlToLinkString($url));

        $url = [
            'plugin' => 'nodes',
            'controller' => 'nodes',
            'action' => 'view',
            'live',
            'long',
            'and',
            'prosper',
        ];
        $expected = 'plugin:nodes/controller:nodes/action:view/live/long/and/prosper';
        $this->assertEquals($expected, $this->Converter->urlToLinkString($url));

        $url = [
            'controller' => 'nodes',
            'action' => 'view',
            'live',
            'long',
            'and',
            'prosper',
        ];
        $expected = 'controller:nodes/action:view/live/long/and/prosper';
        $this->assertEquals($expected, $this->Converter->urlToLinkString($url));

        $url = [
            'admin' => true,
            'controller' => 'nodes',
            'action' => 'edit',
            1,
            'type' => 'blog',
        ];
        $expected = 'admin/controller:nodes/action:edit/1/type:blog';
        $this->assertEquals($expected, $this->Converter->urlToLinkString($url));

        $url = [];
        $this->assertEquals('', $this->Converter->urlToLinkString($url));

        $url = ['some' => 'random', 1, 2, 'array' => 'must', 'work'];
        $expected = 'some:random/1/2/array:must/work';
        $this->assertEquals($expected, $this->Converter->urlToLinkString($url));
    }

    public function testUrlToLinkStringWithQueryStringAndNamedArgs()
    {

        $url = [
            'controller' => 'contacts',
            'action' => 'view',
            'plugin' => 'contacts',
            '?' => [
                'slug' => 'contact',
                'page' => '8',
            ],
        ];
        $expected = 'plugin:contacts/controller:contacts/action:view?slug=contact&page=8';
        $this->assertEquals($expected, $this->Converter->urlToLinkString($url));

        $url = [
            'plugin' => 'nodes',
            'controller' => 'nodes',
            'action' => 'term',
            'type' => 'page',
            '?' => [
                'slug' => 'uncategorized',
            ],
        ];
        $expected = 'plugin:nodes/controller:nodes/action:term/type:page?slug=uncategorized';
        $this->assertEquals($expected, $this->Converter->urlToLinkString($url));
    }

/**
 * testFirstPara
 */
    public function testFirstPara()
    {
        $text = '<p>First paragraph</p>';
        $expected = 'First paragraph';
        $result = $this->Converter->firstPara($text);
        $this->assertEquals($expected, $result);

        $text = '<p class="foo"><span style="font-size: 100%">First<span> paragraph</p>';
        $expected = 'First paragraph';
        $result = $this->Converter->firstPara($text);
        $this->assertEquals($expected, $result);

        $expected = '<p>First paragraph</p>';
        $result = $this->Converter->firstPara($text, ['tag' => true]);
        $this->assertEquals($expected, $result);

        $text = '<p class="foo"><span style="font-size: 100%">First<span> paragraph</p>';
        $expected = '<p><span style="font-size: 100%">First<span> paragraph</p>';
        $result = $this->Converter->firstPara($text, ['tag' => true, 'stripTags' => false]);
        $this->assertEquals($expected, $result);

        $text = "This is the first paragraph.  And this is the second sentence.
This should be the second paragraph.  And this is the fourth sentence, located in the second paragraph";
        $expected = 'This is the first paragraph.  And this is the second sentence.';
        $result = $this->Converter->firstPara($text, ['newline' => true]);
        $this->assertEquals($expected, $result);

        $text = "This is the first paragraph.\nThis should be the second paragraph.";
        $expected = '<p>This is the first paragraph.</p>';
        $result = $this->Converter->firstPara($text, ['tag' => true, 'newline' => true]);
        $this->assertEquals($expected, $result);
    }
}
