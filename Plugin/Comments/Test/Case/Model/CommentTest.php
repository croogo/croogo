<?php
App::uses('CroogoTestCase', 'TestSuite');
App::uses('Model', 'Model');
App::uses('AppModel', 'Model');
App::uses('Comment', 'Comments.Model');

class CommentTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.comments.comment',
		'plugin.nodes.node',
		'plugin.taxonomy.type',
		'plugin.users.user',
	);

	public $Comment;
	protected $_record;


	public function setUp() {
		parent::setUp();
		Configure::write('Comment.level', 10);
		$this->Comment = ClassRegistry::init('Comments.Comment');
		$this->_record = $this->Comment->findById(1);
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->Comment);
	}

}