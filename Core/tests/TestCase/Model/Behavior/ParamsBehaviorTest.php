<?php
namespace Croogo\Core\Test\TestCase\Model\Behavior;

use Croogo\Core\TestSuite\CroogoTestCase;
use Taxonomy\Model\Type;

class ParamsBehaviorTest extends CroogoTestCase
{

    public $fixtures = [
//		'plugin.croogo\users.aco',
//		'plugin.croogo\users.aro',
//		'plugin.croogo\users.aros_aco',
//		'plugin.blocks.block',
//		'plugin.comments.comment',
//		'plugin.contacts.contact',
//		'plugin.translate.i18n',
//		'plugin.croogo\settings.language',
//		'plugin.menus.link',
//		'plugin.menus.menu',
//		'plugin.contacts.message',
//		'plugin.meta.meta',
//		'plugin.croogo\nodes.node',
//		'plugin.taxonomy.model_taxonomy',
//		'plugin.blocks.region',
//		'plugin.croogo\users.role',
//		'plugin.croogo\settings.setting',
//		'plugin.taxonomy.taxonomy',
//		'plugin.taxonomy.term',
//		'plugin.taxonomy.type',
//		'plugin.taxonomy.types_vocabulary',
//		'plugin.croogo\users.user',
//		'plugin.taxonomy.vocabulary',
    ];

    public $Type = null;

/**
 * setUp
 *
 * @return void
 */
    public function setUp()
    {
        parent::setUp();
//		$this->Type = ClassRegistry::init('Taxonomy.Type');
    }

/**
 * tearDown
 *
 * @return void
 */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->Type);
//		ClassRegistry::flush();
    }

    public function testSingle()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->Type->save([
            'title' => 'Article',
            'alias' => 'article',
            'description' => 'Article Types',
            'params' => 'param1=value1',
        ]);
        $type = $this->Type->findByAlias('article');
        $expected = [
            'param1' => 'value1',
        ];
        $this->assertEqual($type['Params'], $expected);
    }

    public function testMultiple()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->Type->save([
            'title' => 'Article',
            'alias' => 'article',
            'description' => 'Article Types',
            'params' => "param1=value1\nparam2=value2",
        ]);
        $type = $this->Type->findByAlias('article');
        $expected = [
            'param1' => 'value1',
            'param2' => 'value2',
        ];
        $this->assertEqual($type['Params'], $expected);
    }

    public function testMixedLineEndings()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->Type->save([
            'title' => 'Article',
            'alias' => 'article',
            'description' => 'Article Types',
            'params' => "param1=value1\r\nparam2=value2\rparam3=value3\nparam4=value4",
        ]);
        $type = $this->Type->findByAlias('article');
        $expected = [
            'param1' => 'value1',
            'param2' => 'value2',
            'param3' => 'value3',
            'param4' => 'value4',
        ];
        $this->assertEqual($type['Params'], $expected);
    }

    public function testEmbeddedOptions()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->Type->save([
            'title' => 'Article',
            'alias' => 'article',
            'description' => 'Article Types',
            'params' => "param1=value1\r\n[options:linkAttr escape=true escapeTitle=false foo=a:b;c:d;e:f]",
        ]);
        $type = $this->Type->findByAlias('article');
        $expected = [
            'param1' => 'value1',
            'linkAttr' => [
                'escape' => 'true',
                'escapeTitle' => 'false',
                'foo' => [
                    'a' => 'b',
                    'c' => 'd',
                    'e' => 'f',
                ],
            ],
        ];
        $this->assertEqual($type['Params'], $expected);
    }

    public function testBoolean()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->Type->save([
            'title' => 'Article',
            'alias' => 'article',
            'description' => 'Article Types',
            'params' => "param1=true\nparam2=false\nparam3=yes\nparam4=no\nparam5=on\nparam6=off",
        ]);
        $type = $this->Type->findByAlias('article');
        $expected = [
            'param1' => true,
            'param2' => false,
            'param3' => true,
            'param4' => false,
            'param5' => true,
            'param6' => false,
        ];
        $this->assertEqual($type['Params'], $expected);
        $this->assertInternalType('boolean', $type['Params']['param1']);
        $this->assertInternalType('boolean', $type['Params']['param2']);
        $this->assertInternalType('boolean', $type['Params']['param3']);
        $this->assertInternalType('boolean', $type['Params']['param4']);
        $this->assertInternalType('boolean', $type['Params']['param5']);
        $this->assertInternalType('boolean', $type['Params']['param6']);
    }

    public function testNumeric()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->Type->save([
            'title' => 'Article',
            'alias' => 'article',
            'description' => 'Article Types',
            'params' => "param1=22\nparam2=0x16\nparam3=0\nparam4=1",
        ]);
        $type = $this->Type->findByAlias('article');
        $expected = [
            'param1' => 22,
            'param2' => 22,
            'param3' => 0,
            'param4' => 1,
        ];
        $this->assertEqual($type['Params'], $expected);
        $this->assertInternalType('integer', $type['Params']['param1']);
        $this->assertInternalType('integer', $type['Params']['param2']);
        $this->assertInternalType('integer', $type['Params']['param3']);
        $this->assertInternalType('integer', $type['Params']['param4']);
    }
}
