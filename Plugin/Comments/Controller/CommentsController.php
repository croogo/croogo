<?php

App::uses('CakeEmail', 'Network/Email');
App::uses('CommentsAppController', 'Comments.Controller');

/**
 * Comments Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo.Comments.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CommentsController extends CommentsAppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'Comments';

/**
 * Components
 *
 * @var array
 * @access public
 */
	public $components = array(
		'Croogo.Akismet',
		'Croogo.Recaptcha',
		'Search.Prg' => array(
			'presetForm' => array(
				'paramType' => 'querystring',
			),
			'commonProcess' => array(
				'paramType' => 'querystring',
				'filterEmpty' => true,
			),
		),
	);

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array('Comments.Comment');

/**
 * Preset Variable Search
 * @var array
 */
	public $presetVars = true;

/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	public function beforeFilter() {
		parent::beforeFilter();
		if ($this->action == 'admin_edit') {
			$this->Security->disabledFields = array('ip');
		}
	}

/**
 * Admin index
 *
 * @return void
 * @access public
 */
	public function admin_index() {
		$this->set('title_for_layout', __d('croogo', 'Comments'));
		$this->Prg->commonProcess();

		$this->Comment->recursive = 0;
		$this->paginate['Comment']['conditions'] = array(
			'Comment.status' => 1,
			'Comment.comment_type' => 'comment',
		);

		$criteria = $this->Comment->parseCriteria($this->request->query);
		if (array_key_exists('Comment.status', $criteria)) {
			$criteria = array_merge($this->paginate['Comment']['conditions'], $criteria);
		}

		$comments = $this->paginate($criteria);
		$this->set(compact('comments', 'criteria'));
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_edit($id = null) {
		$this->set('title_for_layout', __d('croogo', 'Edit Comment'));

		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Comment'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Comment->save($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The Comment has been saved'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__d('croogo', 'The Comment could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Comment->read(null, $id);
		}
	}

/**
 * Admin delete
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Comment'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Comment->delete($id)) {
			$this->Session->setFlash(__d('croogo', 'Comment deleted'), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}
	}

/**
 * Admin process
 *
 * @return void
 * @access public
 */
	public function admin_process() {
		$action = $this->request->data['Comment']['action'];
		$ids = array();
		foreach ($this->request->data['Comment'] as $id => $value) {
			if ($id != 'action' && $value['id'] == 1) {
				$ids[] = $id;
			}
		}

		if (count($ids) == 0 || $action == null) {
			$this->Session->setFlash(__d('croogo', 'No items selected.'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		if ($action == 'delete' &&
			$this->Comment->deleteAll(array('Comment.id' => $ids), true, true)) {
			$this->Session->setFlash(__d('croogo', 'Comments deleted'), 'default', array('class' => 'success'));
		} elseif ($action == 'publish' &&
			$this->Comment->updateAll(array('Comment.status' => true), array('Comment.id' => $ids))) {
			$this->Session->setFlash(__d('croogo', 'Comments published'), 'default', array('class' => 'success'));
		} elseif ($action == 'unpublish' &&
			$this->Comment->updateAll(array('Comment.status' => false), array('Comment.id' => $ids))) {
			$this->Session->setFlash(__d('croogo', 'Comments unpublished'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'An error occurred.'), 'default', array('class' => 'error'));
		}

		$this->redirect(array('action' => 'index'));
	}

/**
 * index
 *
 * @return void
 * @access public
 */
	public function index() {
		$this->set('title_for_layout', __d('croogo', 'Comments'));

		if (!isset($this->request['ext']) ||
			$this->request['ext'] != 'rss') {
			$this->redirect('/');
		}

		$this->paginate['Comment']['order'] = 'Comment.created DESC';
		$this->paginate['Comment']['limit'] = Configure::read('Comment.feed_limit');
		$this->paginate['Comment']['conditions'] = array(
			'Comment.status' => 1,
		);
		$comments = $this->paginate();
		$this->set(compact('comments'));
	}

/**
 * add
 *
 * @param integer $nodeId
 * @param integer $parentId
 * @return void
 * @access public
 */
	public function add($nodeId = null, $parentId = null) {
		if (!$nodeId) {
			$this->Session->setFlash(__d('croogo', 'Invalid Node'), 'default', array('class' => 'error'));
			$this->redirect('/');
		}

		$node = $this->Comment->Node->find('first', array(
			'conditions' => array(
				'Node.id' => $nodeId,
				'Node.status' => 1,
			),
		));

		if (!is_null($parentId) && !$this->Comment->isValidLevel($parentId)) {
			$this->Session->setFlash(__d('croogo', 'Maximum level reached. You cannot reply to that comment.'), 'default', array('class' => 'error'));
			$this->redirect($node['Node']['url']);
		}

		$type = $this->Comment->Node->Taxonomy->Vocabulary->Type->findByAlias($node['Node']['type']);
		$continue = $type['Type']['comment_status'] == 2 && $node['Node']['comment_status'];

		if (!$continue) {
			$this->Session->setFlash(__d('croogo', 'Comments are not allowed.'), 'default', array('class' => 'error'));
			$this->redirect(array(
				'controller' => 'nodes',
				'action' => 'view',
				'type' => $node['Node']['type'],
				'slug' => $node['Node']['slug'],
			));
		}

		// spam protection and captcha
		$continue = $this->_spam_protection($continue, $type, $node);
		$continue = $this->_captcha($continue, $type, $node);
		$success = false;
		if (!empty($this->request->data) && $continue === true) {
			$data = $this->request->data;
			$data['Comment']['ip'] = env('REMOTE_ADDR');
			$userData = array();
			if ($this->Auth->user()) {
				$userData['User'] = $this->Auth->user();
			}

			$success = $this->Comment->add($data, $nodeId, $type, $parentId, $userData);
			if ($success) {
				if ($type['Type']['comment_approve']) {
					$messageFlash = __d('croogo', 'Your comment has been added successfully.');
				} else {
					$messageFlash = __d('croogo', 'Your comment will appear after moderation.');
				}
				$this->Session->setFlash($messageFlash, 'default', array('class' => 'success'));

				if (Configure::read('Comment.email_notification')) {
					$this->_sendEmail($node, $data);
				}

				$this->redirect(Router::url($node['Node']['url'], true) . '#comment-' . $this->Comment->id);
			}
		}

		$this->set(compact('success', 'node', 'type', 'nodeId', 'parentId'));
	}

/**
 * Spam Protection
 *
 * @param boolean $continue
 * @param array $type
 * @param array $node
 * @return boolean
 * @access protected
 */
	protected function _spam_protection($continue, $type, $node) {
		if (!empty($this->request->data) &&
			$type['Type']['comment_spam_protection'] &&
			$continue === true) {
			$this->Akismet->setCommentAuthor($this->request->data['Comment']['name']);
			$this->Akismet->setCommentAuthorEmail($this->request->data['Comment']['email']);
			$this->Akismet->setCommentAuthorURL($this->request->data['Comment']['website']);
			$this->Akismet->setCommentContent($this->request->data['Comment']['body']);
			//$this->Akismet->setPermalink(Router::url($node['Node']['url'], true));
			if ($this->Akismet->isCommentSpam()) {
				$continue = false;
				$this->Session->setFlash(__d('croogo', 'Sorry, the comment appears to be spam.'), 'default', array('class' => 'error'));
			}
		}

		return $continue;
	}

/**
 * Captcha
 *
 * @param boolean $continue
 * @param array $type
 * @param array $node
 * @return boolean
 * @access protected
 */
	protected function _captcha($continue, $type, $node) {
		if (!empty($this->request->data) &&
			$type['Type']['comment_captcha'] &&
			$continue === true &&
			!$this->Recaptcha->valid($this->request)) {
			$continue = false;
			$this->Session->setFlash(__d('croogo', 'Invalid captcha entry'), 'default', array('class' => 'error'));
		}

		return $continue;
	}

/**
 * sendEmail
 *
 * @param array $node Node data
 * @param array $comment Comment data
 * @return void
 * @access protected
 */
	protected function _sendEmail($node, $data) {
		$email = new CakeEmail();
		$commentId = $this->Comment->id;
		try {
			$email->from(Configure::read('Site.title') . ' ' .
				'<croogo@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>')
				->to(Configure::read('Site.email'))
				->subject('[' . Configure::read('Site.title') . '] ' .
					__d('croogo', 'New comment posted under') . ' ' . $node['Node']['title'])
				->viewVars(compact('node', 'data', 'commentId'))
				->template('Comments.comment');
			if ($this->theme) {
				$email->theme($this->theme);
			}
			return $email->send();
		} catch (SocketException $e) {
			$this->log(sprintf('Error sending comment notification: %s', $e->getMessage()));
		}
	}

/**
 * delete
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function delete($id) {
		$success = 0;
		if ($this->Session->check('Auth.User.id')) {
			$userId = $this->Session->read('Auth.User.id');
			$comment = $this->Comment->find('first', array(
				'conditions' => array(
					'Comment.id' => $id,
					'Comment.user_id' => $userId,
				),
			));

			if (isset($comment['Comment']['id']) &&
				$this->Comment->delete($id)) {
				$success = 1;
			}
		}

		$this->set(compact('success'));
	}

}
