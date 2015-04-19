<?php
namespace Croogo\Croogo\Test\TestCase\Model\Behavior;

use Cake\ORM\TableRegistry;
use Croogo\Croogo\TestSuite\CroogoTestCase;
use Croogo\Nodes\Model\Table\NodesTable;
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

	/**
	 * @var NodesTable
	 */
	private $nodesTable;

	/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->nodesTable = TableRegistry::get('Croogo/Nodes.Nodes');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();

		unset($this->nodesTable);
	}

	public function testEncodeWithoutKeys() {
		$array = array('hello', 'world');
		$encoded = $this->nodesTable->encodeData($array);
		$this->assertEquals('["hello","world"]', $encoded);
	}

	public function testEncodeWithKeys() {
		$array = array(
			'first' => 'hello',
			'second' => 'world',
		);
		$encoded = $this->nodesTable->encodeData($array, array(
			'json' => true,
			'trim' => false,
		));
		$this->assertEquals('{"first":"hello","second":"world"}', $encoded);
	}

	public function testDecodeWithoutKeys() {
		$encoded = '["hello","world"]';
		$array = $this->nodesTable->decodeData($encoded);
		$this->assertEquals(array('hello', 'world'), $array);
	}

	public function testDecodeWithKeys() {
		$encoded = '{"first":"hello","second":"world"}';
		$array = $this->nodesTable->decodeData($encoded);
		$this->assertEquals(array(
			'first' => 'hello',
			'second' => 'world',
		), $array);
	}

}
