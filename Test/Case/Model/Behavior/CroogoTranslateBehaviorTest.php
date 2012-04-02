<?php
App::uses('Node', 'Model');
App::uses('CroogoTestCase', 'TestSuite');

class CroogoTranslateBehaviorTest extends CroogoTestCase {

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

	public $Node = null;

	public function startTest($method) {
		$this->Node = ClassRegistry::init('Node');
		$this->Node->Behaviors->attach('CroogoTranslate', array(
			'title' => 'titleTranslation',
		));
	}

	public function testSaveTranslation() {
		$this->Node->id = 2; // About
		$this->Node->locale = 'ben';
		$this->Node->saveTranslation(array(
			'Node' => array(
				'title' => 'About [Translated in Bengali]',
			),
		));
		$about = $this->Node->findById('2');
		$this->assertEqual($about['Node']['title'], 'About [Translated in Bengali]');
	}

	public function endTest($method) {
		unset($this->Node);
		ClassRegistry::flush();
	}
}
