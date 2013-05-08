<?php
App::uses('Type', 'Taxonomy.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

CakePlugin::load('Translate');

class ParamsBehaviorTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.users.aco',
		'plugin.users.aro',
		'plugin.users.aros_aco',
		'plugin.blocks.block',
		'plugin.comments.comment',
		'plugin.contacts.contact',
		'plugin.translate.i18n',
		'plugin.settings.language',
		'plugin.menus.link',
		'plugin.menus.menu',
		'plugin.contacts.message',
		'plugin.meta.meta',
		'plugin.nodes.node',
		'plugin.taxonomy.nodes_taxonomy',
		'plugin.blocks.region',
		'plugin.users.role',
		'plugin.settings.setting',
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

	public function testMixedLineEndings() {
		$this->Type->save(array(
			'title' => 'Article',
			'alias' => 'article',
			'description' => 'Article Types',
			'params' => "param1=value1\r\nparam2=value2\rparam3=value3\nparam4=value4",
		));
		$type = $this->Type->findByAlias('article');
		$expected = array(
			'param1' => 'value1',
			'param2' => 'value2',
			'param3' => 'value3',
			'param4' => 'value4',
		);
		$this->assertEqual($type['Params'], $expected);
	}

}
