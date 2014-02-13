<?php

App::uses('CroogoTestCase', 'Croogo.TestSuite');

class CommentableBehaviorTest extends CroogoTestCase {

	public $setupSettings = false;

	public $fixtures = array(
		'plugin.comments.comment',
		'plugin.nodes.node',
		'plugin.users.user',
		'plugin.taxonomy.type',
	);

	public function setUp() {
		$this->Comment = ClassRegistry::init('Comments.Comment');
		$this->Comment->bindModel(array(
			'belongsTo' => array(
				'Node' => array(
					'className' => 'Node',
					'foreignKey' => 'foreign_key',
					'conditions' => array(
						'model' => 'Node',
					),
				),
			),
		), false);

		$this->Comment->Node->Behaviors->load('Comments.Commentable');
	}

	public function tearDown() {
		ClassRegistry::flush();
	}

/**
 * Test Get Type Setting
 */
	public function testGetTypeSetting() {
		$result = $this->Comment->Node->getTypeSetting(array(
			'Node' => array(
				'type' => 'blog',
			),
		));
		$expected = array(
			'commentable' => true,
			'autoApprove' => true,
			'spamProtection' => false,
			'captchaProtection' => false,
		);
		$this->assertEquals($expected, $result);
	}

}
