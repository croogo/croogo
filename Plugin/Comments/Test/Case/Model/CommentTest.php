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

	public function testAdd() {
		$oldCount = $this->Comment->find('count');
		$data = array(
			'Comment' => array(
				'name' => 'Test Visitor',
				'email' => 'visitor@test.fr',
				'website' => 'http://www.test.fr',
				'body' => 'TESTEH'
			)
		);

		$result = (bool) $this->Comment->add(
			$data,
			1,
			array('Type' => array('alias' => 'blog', 'comment_status' => 2, 'comment_approve' => 2))
		);
		$newCount = $this->Comment->find('count');

		$this->assertEquals($oldCount + 1, $newCount);
		$this->assertTrue($result);
	}

	public function testAddWithParentId() {
		$oldCount = $this->Comment->find('count');
		$data = array(
			'Comment' => array(
				'name' => 'Test Visitor',
				'email' => 'visitor@test.fr',
				'website' => 'http://www.test.fr',
				'body' => 'TESTEH',
			)
		);

		$result = (bool) $this->Comment->add(
			$data,
			1,
			array('Type' => array('alias' => 'blog', 'comment_status' => 2, 'comment_approve' => 2)),
			1
		);
		$newCount = $this->Comment->find('count');
		$newComment = $this->Comment->find('first', array('order' => 'Comment.created DESC'));

		$this->assertEquals(1, $newComment['Comment']['parent_id']);
		$this->assertEquals($oldCount + 1, $newCount);
		$this->assertTrue($result);
	}

	public function testAddWithParentId_When_ExceedingLevel_AddCommentWithoutParentId() {
		$oldConf = Configure::read('Comment.level');
		Configure::write('Comment.level', 1);
		$oldCount = $this->Comment->find('count');
		$data = array(
			'Comment' => array(
				'name' => 'Test Visitor',
				'email' => 'visitor@test.fr',
				'website' => 'http://www.test.fr',
				'body' => 'TESTEH',
			)
		);

		$result = (bool) $this->Comment->add(
			$data,
			1,
			array('Type' => array('alias' => 'blog', 'comment_status' => 2, 'comment_approve' => 2)),
			1
		);
		Configure::write('Comment.level', $oldConf);
		$newCount = $this->Comment->find('count');
		$newComment = $this->Comment->find('first', array('order' => 'Comment.created DESC'));

		$this->assertEmpty($newComment['Comment']['parent_id']);
		$this->assertEquals($oldCount + 1, $newCount);
		$this->assertTrue($result);
	}

	public function testAdd_ThrowsException_When_InvalidNodeId() {
		$this->setExpectedException('NotFoundException');
		$this->Comment->add(
			array('Comment' => array('name', 'email', 'body')),
			'invalid',
			array()
		);
	}

	public function testAdd_ThrowsException_When_InvalidParentId() {
		$this->setExpectedException('NotFoundException');
		$data = array(
			'Comment' => array(
				'name' => 'Test Visitor',
				'email' => 'visitor@test.fr',
				'website' => 'http://www.test.fr',
				'body' => 'TESTEH'
			)
		);
		$this->Comment->add(
			$data,
			1,
			array('Type' => array('alias' => 'blog', 'comment_status' => 2, 'comment_approve' => 2)),
			'invalid'
		);
	}

}