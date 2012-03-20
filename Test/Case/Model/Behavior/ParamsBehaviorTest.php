<?php
App::import('Model', 'Type');
class ParamsBehaviorTest extends CakeTestCase {

	public $fixtures = array(
		'aco',
		'aro',
		'aros_aco',
		'block',
		'comment',
		'contact',
		'i18n',
		'language',
		'link',
		'menu',
		'message',
		'meta',
		'node',
		'nodes_taxonomy',
		'region',
		'role',
		'setting',
		'taxonomy',
		'term',
		'type',
		'types_vocabulary',
		'user',
		'vocabulary',
	);

	public $Type = null;

	public function startTest($method) {
		$this->Type = ClassRegistry::init('Type');
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

	public function endTest($method) {
		unset($this->Type);
		ClassRegistry::flush();
	}
}
