<?php
App::uses('CommentsController', 'Comments.Controller');
App::uses('CroogoControllerTestCase', 'Croogo.TestSuite');
App::uses('CroogoTestFixture', 'Croogo.TestSuite');

class CommentsControllerTest extends CroogoControllerTestCase {

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

	protected $_level;

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_level = Configure::write('Comment.level');
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		$_SERVER['SERVER_NAME'] = 'localhost';
		$this->CommentsController = $this->generate('Comments.Comments', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->CommentsController->Auth
			->staticExpects($this->any())
			->method('user')
			->will($this->returnCallback(array($this, 'authUserCallback')));
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		Configure::write('Comment.level', $this->_level);
		unset($this->CommentsController);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/comments/index');
		$this->assertNotEmpty($this->vars['comments']);
	}

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->expectFlashAndRedirect('The Comment has been saved');
		$this->testAction('/admin/comments/comments/edit/1', array(
			'data' => array(
				'Comment' => array(
					'id' => 1, // Mr Croogo
					'name' => 'Mr Croogo [modified]',
					'email' => 'contact@example.com',
					'body' => 'lots of text...',
				),
			),
		));
		$result = $this->CommentsController->Comment->findById(1);
		$this->assertEquals('Mr Croogo [modified]', $result['Comment']['name']);
	}

/**
 * testAdminDelete
 *
 * @return void
 */
	public function testAdminDelete() {
		$this->expectFlashAndRedirect('Comment deleted');
		$this->testAction('/admin/comments/comments/delete/1');
		$hasAny = $this->CommentsController->Comment->hasAny(array(
			'Comment.id' => 1,
		));
		$this->assertFalse($hasAny);
	}

/**
 * testAdminProcessDelete
 *
 * @return void
 */
	public function testAdminProcessDelete() {
		$this->expectFlashAndRedirect('Comments deleted');
		$this->testAction('/admin/comments/comments/process', array(
			'data' => array(
				'Comment' => array(
					'action' => 'delete',
					'1' => array('id' => 1),
				),
			),
		));
		$list = $this->CommentsController->Comment->find('list', array(
			'fields' => array(
				'id',
				'name',
			),
			'order' => 'Comment.lft ASC',
		));
		$this->assertEqual($list, array(2 => 'Mrs Croogo'));
	}

/**
 * testAdminProcessPublish
 *
 * @return void
 */
	public function testAdminProcessPublish() {
		// unpublish a Comment for testing
		$this->CommentsController->Comment->id = 1;
		$this->CommentsController->Comment->saveField('status', 0);
		$this->CommentsController->Comment->id = false;
		$comment = $this->CommentsController->Comment->hasAny(array(
			'id' => 1,
			'status' => 0,
		));
		$this->assertTrue($comment);

		$this->expectFlashAndRedirect('Comments published');

		$this->testAction('/admin/comments/comments/process', array(
			'data' => array(
				'Comment' => array(
					'action' => 'publish',
					'1' => array('id' => 1),
				),
			),
		));

		$list = $this->CommentsController->Comment->find('list', array(
			'conditions' => array(
				'Comment.status' => 1,
			),
			'fields' => array(
				'id',
				'name',
			),
			'order' => 'Comment.lft ASC',
		));
		$this->assertEqual($list, array(
			'1' => 'Mr Croogo',
		));
	}

/**
 * testAdminProcessUnpublish
 *
 * @return void
 */
	public function testAdminProcessUnpublish() {
		$this->expectFlashAndRedirect('Comments unpublished');
		$this->testAction('/admin/comments/comments/process', array(
			'data' => array(
				'Comment' => array(
					'action' => 'unpublish',
					'1' => array('id' => 1),
				),
			),
		));
		$list = $this->CommentsController->Comment->find('list', array(
			'conditions' => array(
				'Comment.status' => 1,
			),
			'fields' => array(
				'id',
				'name',
			),
			'order' => 'Comment.lft ASC',
		));
		$this->assertEqual($list, array());
	}

/**
 * testAdd
 */
	public function testAdd() {
		Configure::write('Comment.email_notification', 1);
		$Comments = $this->generate('Comments', array(
			'methods' => array(
				'_sendEmail',
			),
			'components' => array(
				'Session',
			),
		));
		$Comments->plugin = 'Comments';
		$Comments
			->expects($this->once())
			->method('_sendEmail')
			->will($this->returnValue(true));
		$Comments->request->params['action'] = 'add';
		$Comments->request->params['url']['url'] = 'comments/comments/add';
		$Comments->Components->trigger('initialize', array(&$Comments));

		$Comments->Components->trigger('startup', array(&$Comments));
		$Comments->request->data['Comment'] = array(
			'name' => 'John Smith',
			'email' => 'john.smith@example.com',
			'website' => 'http://example.com',
			'body' => 'text here...',
		);
		$node = $Comments->Comment->Node->findBySlug('hello-world');
		$Comments->add($node['Node']['id']);
		$this->assertEqual($Comments->viewVars['success'], 1);

		$comments = $Comments->Comment->generateTreeList(array('Comment.node_id' => $node['Node']['id']), '{n}.Comment.id', '{n}.Comment.name');
		$commenters = array_values($comments);
		$this->assertEqual($commenters, array('Mr Croogo', 'Mrs Croogo', 'John Smith'));

		$Comments->testView = true;
		$output = $Comments->render('add');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

/**
 * testAddWithParent
 */
	public function testAddWithParent() {
		Configure::write('Comment.email_notification', 1);
		$Comments = $this->generate('Comments', array(
			'methods' => array('_sendEmail'),
		));
		$Comments->plugin = 'Comments';
		$Comments
			->expects($this->once())
			->method('_sendEmail')
			->will($this->returnValue(true));
		$Comments->request->params['action'] = 'add';
		$Comments->request->params['url']['url'] = 'comments/comments/add';
		$Comments->Components->trigger('initialize', array(&$Comments));
		$Comments->Components->trigger('startup', array(&$Comments));

		$Comments->request->data['Comment'] = array(
			'name' => 'John Smith',
			'email' => 'john.smith@example.com',
			'website' => 'http://example.com',
			'body' => 'text here...',
		);
		$node = $Comments->Comment->Node->findBySlug('hello-world');

		Configure::write('Comment.level', 2);
		$Comments->add($node['Node']['id'], 1); // under the comment by Mr Croogo
		$this->assertEqual($Comments->viewVars['success'], 1);

		$comments = $Comments->Comment->generateTreeList(array('Comment.node_id' => $node['Node']['id']), '{n}.Comment.id', '{n}.Comment.name');
		$commenters = array_values($comments);
		$this->assertEqual($commenters, array('Mr Croogo', '_John Smith', 'Mrs Croogo'));

		$Comments->testView = true;
		$output = $Comments->render('add');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

/**
 * testAddWithoutEmailNotification
 */
	public function testAddWithoutEmailNotification() {
		Configure::write('Comment.email_notification', 0);
		$Comments = $this->generate('Comments', array(
			'components' => array(
				'Session',
			),
		));
		$Comments->plugin = 'Comments';
		$Comments->request->params['action'] = 'add';
		$Comments->request->params['url']['url'] = 'comments/comments/add';
		$Comments->startupProcess();
		$Comments->request->data['Comment'] = array(
			'name' => 'John Smith',
			'email' => 'john.smith@example.com',
			'website' => 'http://example.com',
			'body' => 'text here...',
		);
		$node = $Comments->Comment->Node->findBySlug('hello-world');
		$Comments->add($node['Node']['id']);
		$this->assertEqual($Comments->viewVars['success'], 1);

		$comments = $Comments->Comment->generateTreeList(array('Comment.node_id' => $node['Node']['id']), '{n}.Comment.id', '{n}.Comment.name');
		$commenters = array_values($comments);
		$this->assertEqual($commenters, array('Mr Croogo', 'Mrs Croogo', 'John Smith'));

		$Comments->testView = true;
		$output = $Comments->render('add');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

/**
 * testAddNotAllowedByType
 */
	public function testAddNotAllowedByType() {
		$Type = ClassRegistry::init('Taxonomy.Type');
		$Type->id = 2;
		$Type->saveField('comment_status', 0);

		$this->CommentsController->Session
			->expects($this->once())
			->method('setFlash')
			->with(
				$this->equalTo('Comments are not allowed.'),
				$this->equalTo('default'),
				$this->equalTo(array('class' => 'error'))
			);
		$this->CommentsController->request->params['action'] = 'add';
		$this->CommentsController->request->params['url']['url'] = 'comments/add';

		$this->CommentsController->request->data['Comment'] = array(
			'name' => 'John Smith',
			'email' => 'john.smith@example.com',
			'website' => 'http://example.com',
			'body' => 'text here...',
		);
		$this->CommentsController->add(1);
		$this->assertEqual($this->CommentsController->viewVars['success'], 0);
	}

/**
 * testAddShouldWorkWhenLoggedIn
 */
	public function testAddShouldWorkWhenLoggedIn() {
		Configure::write('Comment.email_notification', 0);
		$this->CommentsController->request->params['action'] = 'add';
		$this->CommentsController->request->params['url']['url'] = 'comments/add';

		$this->CommentsController->request->data['Comment'] = array(
			'name' => 'John Smith',
			'email' => 'john.smith@example.com',
			'website' => 'http://example.com',
			'body' => 'text here...',
		);
		$this->CommentsController->add(1);

		$this->assertEqual($this->CommentsController->viewVars['success'], 1);
	}

}