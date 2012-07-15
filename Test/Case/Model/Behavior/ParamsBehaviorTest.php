<?php
App::uses('Type', 'Model');
App::uses('CroogoTestCase', 'TestSuite');

class ParamsBehaviorTest extends CroogoTestCase {

	public $fixtures = array(
		'aco',
		'aro',
		'aros_aco',
		'plugin.blocks.block',
		'comment',
		'contact',
		'i18n',
		'language',
		'plugin.menus.link',
		'plugin.menus.menu',
		'message',
		'plugin.meta.meta',
		'node',
		'plugin.taxonomy.nodes_taxonomy',
		'plugin.blocks.region',
		'plugin.users.role',
		'setting',
		'plugin.taxonomy.taxonomy',
		'plugin.taxonomy.term',
		'plugin.taxonomy.type',
		'plugin.taxonomy.types_vocabulary',
		'plugin.users.user',
		'plugin.taxonomy.vocabulary',
	);

	public $Type = null;

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Type = ClassRegistry::init('Taxonomy.Type');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Type);
		ClassRegistry::flush();
	}

	public function testSingle() {
		$this->Type->save(array(
			'title' => 'Article',
			'alias' => 'article',
			'description' => 'Article Types',
			'params' => 'param1=value1',
		));
		$type = $this->Type->findByAlias('article');
		$expected = array(
			'param1' => 'value1',
		);
		$this->assertEqual($type['Params'], $expected);
	}

	public function testMultiple() {
		$this->Type->save(array(
			'title' => 'Article',
			'alias' => 'article',
			'description' => 'Article Types',
			'params' => "param1=value1\nparam2=value2",
		));
		$type = $this->Type->findByAlias('article');
		$expected = array(
			'param1' => 'value1',
			'param2' => 'value2',
		);
		$this->assertEqual($type['Params'], $expected);
	}

}
