<?php
App::uses('Comment', 'Comments.Model');
App::uses('CroogoTestCase', 'TestSuite');

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

/**
 * testAdd
 */
	public function testAdd() {
		$oldCount = $this->Comment->find('count');
		$data = array(
			'Comment' => array(
				'name' => 'Test Visitor',
				'email' => 'visitor@test.fr',
				'website' => 'http://www.test.fr',
				'body' => 'TESTEH',
				'ip' => '127.0.0.1'
			)
		);

		$result = $this->Comment->add(
			$data,
			1,
			array('Type' => array('alias' => 'blog', 'comment_status' => 2, 'comment_approve' => 2))
		);
		$this->assertTrue($result);

		$newCount = $this->Comment->find('count');
		$newComment = $this->Comment->find('first', array('order' => 'Comment.created DESC'));
		$this->assertEquals($oldCount + 1, $newCount);
		$this->assertEquals(2, $newComment['Comment']['status']);
	}

/**
 * testAddWithParentId
 */
	public function testAddWithParentId() {
		$oldCount = $this->Comment->find('count');
		$data = array(
			'Comment' => array(
				'name' => 'Test Visitor',
				'email' => 'visitor@test.fr',
				'website' => 'http://www.test.fr',
				'body' => 'TESTEH',
				'ip' => '127.0.0.1'
			)
		);

		$result = $this->Comment->add(
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

/**
 * testAddCommentIsRejectedWhenLevelIsExceeded
 */
	public function testAddCommentIsRejectedWhenLevelIsExceeded() {
		$oldConf = Configure::read('Comment.level');
		Configure::write('Comment.level', 1);
		$oldCount = $this->Comment->find('count');
		$data = array(
			'Comment' => array(
				'name' => 'Test Visitor',
				'email' => 'visitor@test.fr',
				'website' => 'http://www.test.fr',
				'body' => 'TESTEH',
				'ip' => '127.0.0.1'
			)
		);

		$result = $this->Comment->add(
			$data,
			1,
			array('Type' => array('alias' => 'blog', 'comment_status' => 2, 'comment_approve' => 2)),
			1
		);

		$this->assertFalse($result);
		$newCount = $this->Comment->find('count');
		$newComment = $this->Comment->find('first', array('order' => 'Comment.created DESC'));

		$this->assertFalse($result);
		$this->assertEquals($oldCount, $newCount);
	}

/**
 * testAddCommentThrowsExceptionWithInvalidNodeId
 */
	public function testAddCommentThrowsExceptionWithInvalidNodeId() {
		$this->setExpectedException('NotFoundException');
		$this->Comment->add(
			array('Comment' => array('name', 'email', 'body')),
			'invalid',
			array()
		);
	}

/**
 * testAddCommentThrowsExceptionWithInvalidParentId
 */
	public function testAddCommentThrowsExceptionWithInvalidParentId() {
		$this->setExpectedException('NotFoundException');
		$data = array(
			'Comment' => array(
				'name' => 'Test Visitor',
				'email' => 'visitor@test.fr',
				'website' => 'http://www.test.fr',
				'body' => 'TESTEH',
				'ip' => '127.0.0.1'
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