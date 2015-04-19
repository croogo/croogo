<?php
namespace Croogo\Croogo\Test\TestCase\Model\Behavior;

use Croogo\Croogo\TestSuite\CroogoTestCase;
use Nodes\Model\Node;
class EncoderBehaviorTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.croogo\users.aco',
		'plugin.croogo\users.aro',
		'plugin.croogo\users.aros_aco',
//		'plugin.blocks.block',
//		'plugin.comments.comment',
//		'plugin.contacts.contact',
//		'plugin.translate.i18n',
		'plugin.croogo\settings.language',
//		'plugin.menus.link',
//		'plugin.menus.menu',
//		'plugin.contacts.message',
//		'plugin.meta.meta',
		'plugin.croogo\nodes.node',
//		'plugin.taxonomy.model_taxonomy',
//		'plugin.blocks.region',
//		'plugin.users.role',
//		'plugin.settings.setting',
//		'plugin.taxonomy.taxonomy',
//		'plugin.taxonomy.term',
//		'plugin.taxonomy.type',
//		'plugin.taxonomy.types_vocabulary',
		'plugin.croogo\users.user',
//		'plugin.taxonomy.vocabulary',
	);

	public $Node = null;

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
//		$this->Node = ClassRegistry::init('Nodes.Node');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Node);
//		ClassRegistry::flush();
	}

	public function testEncodeWithoutKeys() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$array = array('hello', 'world');
		$encoded = $this->Node->encodeData($array);
		$this->assertEqual($encoded, '["hello","world"]');
	}

	public function testEncodeWithKeys() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$array = array(
			'first' => 'hello',
			'second' => 'world',
		);
		$encoded = $this->Node->encodeData($array, array(
			'json' => true,
			'trim' => false,
		));
		$this->assertEqual($encoded, '{"first":"hello","second":"world"}');
	}

	public function testDecodeWithoutKeys() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$encoded = '["hello","world"]';
		$array = $this->Node->decodeData($encoded);
		$this->assertEqual($array, array('hello', 'world'));
	}

	public function testDecodeWithKeys() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$encoded = '{"first":"hello","second":"world"}';
		$array = $this->Node->decodeData($encoded);
		$this->assertEqual($array, array(
			'first' => 'hello',
			'second' => 'world',
		));
	}

}
