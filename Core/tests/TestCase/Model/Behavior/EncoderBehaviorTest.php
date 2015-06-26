<?php
namespace Croogo\Croogo\Test\TestCase\Model\Behavior;

use Cake\ORM\TableRegistry;
use Croogo\Core\TestSuite\CroogoTestCase;
use Croogo\Nodes\Model\Table\NodesTable;
class EncoderBehaviorTest extends CroogoTestCase {

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
