<?php
App::uses('CommentsController', 'Controller');
App::uses('CroogoControllerTestCase', 'TestSuite');
App::uses('CroogoTestFixture', 'TestSuite');

class TestCommentsController extends CommentsController {

	public $name = 'Comments';

	public $autoRender = false;

	public $testView = false;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}

	public function render($action = null, $layout = null, $file = null) {
		if (!$this->testView) {
			$this->renderedAction = $action;
		} else {
			return parent::render($action, $layout, $file);
		}
	}

	protected function _stop($status = 0) {
		$this->stopped = $status;
	}

	public function securityError($type) {
	}

}

class CommentsControllerTest extends CroogoControllerTestCase {

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

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		$_SERVER['SERVER_NAME'] = 'localhost';
		$request = new CakeRequest();
		$response = new CakeResponse();
		$this->Comments = new TestCommentsController($request, $response);
		$this->Comments->constructClasses();
		$this->Comments->request->params['controller'] = 'Comments';
		$this->Comments->request->params['pass'] = array();
		$this->Comments->request->params['named'] = array();

		$this->CommentsController = $this->generate('Comments', array(
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
		unset($this->Comments);
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
		$this->CommentsController->Session
			->expects($this->once())
			->method('setFlash')
			->with(
				$this->equalTo('The Comment has been saved'),
				$this->equalTo('default'),
				$this->equalTo(array('class' => 'success'))
			);
		$this->CommentsController
			->expects($this->once())
			->method('redirect');
		$this->testAction('/admin/comments/edit/1', array(
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

	public function testAdminDelete() {
		$this->Comments->request->params['action'] = 'admin_delete';
		$this->Comments->request->params['url']['url'] = 'admin/comments/delete';
		$this->Comments->Components->trigger('initialize', array(&$this->Comments));
		$this->Comments->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Comments->Components->trigger('startup', array(&$this->Comments));
		$this->Comments->admin_delete(1);
		$this->assertEqual($this->Comments->redirectUrl, array('action' => 'index'));

		$hasAny = $this->Comments->Comment->hasAny(array(
			'Comment.id' => 1,
		));
		$this->assertFalse($hasAny);
	}

	public function testAdminProcessDelete() {
		$this->Comments->request->params['action'] = 'admin_process';
		$this->Comments->request->params['url']['url'] = 'admin/comments/process';
		$this->Comments->Components->trigger('initialize', array(&$this->Comments));
		$this->Comments->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Comments->Components->trigger('startup', array(&$this->Comments));

		$this->Comments->request->data['Comment'] = array(
			'action' => 'delete',
			'1' => array(
				'id' => 1,
			),
		);
		$this->Comments->admin_process();
		$this->assertEqual($this->Comments->redirectUrl, array('action' => 'index'));
		$list = $this->Comments->Comment->find('list', array(
			'fields' => array(
				'id',
				'name',
			),
			'order' => 'Comment.lft ASC',
		));
		$this->assertEqual($list, array(2 => 'Mrs Croogo'));
	}

	public function testAdminProcessPublish() {
		$this->Comments->request->params['action'] = 'admin_process';
		$this->Comments->request->params['url']['url'] = 'admin/comments/process';
		$this->Comments->Components->trigger('initialize', array(&$this->Comments));
		$this->Comments->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Comments->Components->trigger('startup', array(&$this->Comments));

		// unpublish a Comment for testing
		$this->Comments->Comment->id = 1;
		$this->Comments->Comment->saveField('status', 0);
		$this->Comments->Comment->id = false;
		$comment = $this->Comments->Comment->hasAny(array(
			'id' => 1,
			'status' => 0,
		));
		$this->assertTrue($comment);

		$this->Comments->request->data['Comment'] = array(
			'action' => 'publish',
			'1' => array(
				'id' => 1,
			),
		);
		$this->Comments->admin_process();
		$this->assertEqual($this->Comments->redirectUrl, array('action' => 'index'));
		$list = $this->Comments->Comment->find('list', array(
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

	public function testAdminProcessUnpublish() {
		$this->Comments->request->params['action'] = 'admin_process';
		$this->Comments->request->params['url']['url'] = 'admin/comments/process';
		$this->Comments->Components->trigger('initialize', array(&$this->Comments));
		$this->Comments->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Comments->Components->trigger('startup', array(&$this->Comments));

		$this->Comments->request->data['Comment'] = array(
			'action' => 'unpublish',
			'1' => array(
				'id' => 1,
			),
		);
		$this->Comments->admin_process();
		$this->assertEqual($this->Comments->redirectUrl, array('action' => 'index'));
		$list = $this->Comments->Comment->find('list', array(
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
			'components' => array(
				'Email' => array('send'),
				'Session',
			),
		));
		$Comments->Email
			->expects($this->once())
			->method('send')
			->will($this->returnValue(true));
		$Comments->request->params['action'] = 'add';
		$Comments->request->params['url']['url'] = 'comments/add';
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
			'components' => array(
				'Email' => array('send'),
			),
		));
		$Comments->Email
			->expects($this->once())
			->method('send')
			->will($this->returnValue(true));
		$Comments->request->params['action'] = 'add';
		$Comments->request->params['url']['url'] = 'comments/add';
		$Comments->Components->trigger('initialize', array(&$Comments));
		$Comments->Components->trigger('startup', array(&$Comments));

		$Comments->request->data['Comment'] = array(
			'name' => 'John Smith',
			'email' => 'john.smith@example.com',
			'website' => 'http://example.com',
			'body' => 'text here...',
		);
		$node = $Comments->Comment->Node->findBySlug('hello-world');
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
		$Comments->request->params['action'] = 'add';
		$Comments->request->params['url']['url'] = 'comments/add';
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
 * testAddNotAllowedByType
 */
	public function testAddNotAllowedByType() {
		$Type = ClassRegistry::init('Type');
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

}