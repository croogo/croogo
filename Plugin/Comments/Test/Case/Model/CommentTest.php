<?php
App::uses('Comment', 'Comments.Model');
App::uses('CroogoTestCase', 'TestSuite');

class CommentTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.comments.comment',
		'plugin.meta.meta',
		'plugin.nodes.node',
		'plugin.taxonomy.nodes_taxonomy',
		'plugin.taxonomy.taxonomy',
		'plugin.taxonomy.type',
		'plugin.users.user',
	);

	public $Comment;
	protected $_level;
	protected $_record;


	public function setUp() {
		parent::setUp();
		$this->_level = Configure::read('Comment.level');
		Configure::write('Comment.level', 10);
		$this->Comment = ClassRegistry::init('Comments.Comment');
		$this->_record = $this->Comment->findById(1);
	}

	public function tearDown() {
		parent::tearDown();
		Configure::write('Comment.level', $this->_level);
		unset($this->Comment);
	}

	public function testMandatoryFields() {
		$record = array('id' => '');
		$expectedKeys = array('body', 'name', 'email');

		$this->Comment->save($record);
		$this->assertEquals($expectedKeys, array_keys($this->Comment->validationErrors));
	}

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
				'ip' => '127.0.0.1'
			)
		);

		$result = $this->Comment->add(
			$data,
			1,
			array('Type' => array('alias' => 'blog', 'comment_status' => 2, 'comment_approve' => 2)),
			1
		);

		Configure::write('Comment.level', $oldConf);
		$newCount = $this->Comment->find('count');
		$newComment = $this->Comment->find('first', array('order' => 'Comment.created DESC'));

		$this->assertTrue($result);
		$this->assertEquals($oldCount + 1, $newCount);
		$this->assertEmpty($newComment['Comment']['parent_id']);
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