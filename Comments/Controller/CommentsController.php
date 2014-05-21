<?php

namespace Croogo\Comments\Controller;
App::uses('CakeEmail', 'Network/Email');
App::uses('CommentsAppController', 'Comments.Controller');

/**
 * Comments Controller
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
		'Croogo.BulkProcess',
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
			'Comment.status' => $this->Comment->status('approval'),
			'Comment.comment_type' => 'comment',
		);

		$criteria = $this->Comment->parseCriteria($this->Prg->parsedParams());
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
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Comment->save($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The Comment has been saved'), 'default', array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
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
			return $this->redirect(array('action' => 'index'));
		}
		if ($this->Comment->delete($id)) {
			$this->Session->setFlash(__d('croogo', 'Comment deleted'), 'default', array('class' => 'success'));
			return $this->redirect(array('action' => 'index'));
		}
	}

/**
 * Admin process
 *
 * @return void
 * @access public
 */
	public function admin_process() {
		$Comment = $this->{$this->modelClass};
		list($action, $ids) = $this->BulkProcess->getRequestVars($Comment->alias);

		$options = array(
			'messageMap' => array(
				'delete' => __d('croogo', 'Comments deleted'),
				'publish' => __d('croogo', 'Comments published'),
				'unpublish' => __d('croogo', 'Comments unpublished'),
			),
		);
		return $this->BulkProcess->process($Comment, $action, $ids, $options);
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
			return $this->redirect('/');
		}

		$this->paginate['Comment']['order'] = 'Comment.created DESC';
		$this->paginate['Comment']['limit'] = Configure::read('Comment.feed_limit');
		$this->paginate['Comment']['conditions'] = array(
			'Comment.status' => $this->Comment->status('approval'),
		);
		$comments = $this->paginate();
		$this->set(compact('comments'));
	}

/**
 * add
 *
 * @param integer $foreignKey
 * @param integer $parentId
 * @return void
 * @access public
 * @throws UnexpectedValueException
 */
	public function add($model, $foreignKey = null, $parentId = null) {
		if (!$foreignKey) {
			$this->Session->setFlash(__d('croogo', 'Invalid id'), 'default', array('class' => 'error'));
			return $this->redirect('/');
		}

		if (empty($this->Comment->{$model})) {
			throw new UnexpectedValueException(
				sprintf('%s not configured for Comments', $model)
			);
		}

		$Model = $this->Comment->{$model};
		$data = $Model->find('first', array(
			'conditions' => array(
				$Model->escapeField($Model->primaryKey) => $foreignKey,
				$Model->escapeField('status') => $Model->status('approval'),
			),
		));

		if (isset($data[$Model->alias]['path'])) {
			$redirectUrl = $data[$Model->alias]['path'];
		} else {
			$redirectUrl = $this->referer();
		}

		if (!is_null($parentId) && !$this->Comment->isValidLevel($parentId)) {
			$this->Session->setFlash(__d('croogo', 'Maximum level reached. You cannot reply to that comment.'), 'default', array('class' => 'error'));
			return $this->redirect($redirectUrl);
		}

		$typeSetting = $Model->getTypeSetting($data);
		extract(array_intersect_key($typeSetting, array(
			'commentable' => null,
			'autoApprove' => null,
			'spamProtection' => null,
			'captchaProtection' => null,
			))
		);
		$continue = $commentable && $data[$Model->alias]['comment_status'];

		if (!$continue) {
			$this->Session->setFlash(__d('croogo', 'Comments are not allowed.'), 'default', array('class' => 'error'));
			return $this->redirect($redirectUrl);
		}

		// spam protection and captcha
		$continue = $this->_spamProtection($continue, $spamProtection, $data);
		$continue = $this->_captcha($continue, $captchaProtection, $data);
		$success = false;
		if (!empty($this->request->data) && $continue === true) {
			$comment = $this->request->data;
			$comment['Comment']['ip'] = env('REMOTE_ADDR');
			$comment['Comment']['status'] = $autoApprove ? CroogoStatus::APPROVED : CroogoStatus::PENDING;
			$userData = array();
			if ($this->Auth->user()) {
				$userData['User'] = $this->Auth->user();
			}

			$options = array(
				'parentId' => $parentId,
				'userData' => $userData,
			);
			$success = $this->Comment->add($comment, $model, $foreignKey, $options);
			if ($success) {
				if ($autoApprove) {
					$messageFlash = __d('croogo', 'Your comment has been added successfully.');
				} else {
					$messageFlash = __d('croogo', 'Your comment will appear after moderation.');
				}
				$this->Session->setFlash($messageFlash, 'default', array('class' => 'success'));

				if (Configure::read('Comment.email_notification')) {
					$this->_sendEmail($data, $comment);
				}

				return $this->redirect(Router::url($data[$Model->alias]['url'], true) . '#comment-' . $this->Comment->id);
			}
		}

		$this->set(compact('success', 'data', 'type', 'model', 'foreignKey', 'parentId'));
	}

/**
 * Spam Protection
 *
 * @param boolean $continue
 * @param boolean $spamProtection
 * @param array $node
 * @return boolean
 * @access protected
 * @deprecated This method will be renamed to _spamProtection() in the future
 */
	protected function _spamProtection($continue, $spamProtection, $node) {
		if (!empty($this->request->data) &&
			$spamProtection &&
			$continue === true) {
			$this->Akismet->setCommentAuthor($this->request->data['Comment']['name']);
			$this->Akismet->setCommentAuthorEmail($this->request->data['Comment']['email']);
			$this->Akismet->setCommentAuthorURL($this->request->data['Comment']['website']);
			$this->Akismet->setCommentContent($this->request->data['Comment']['body']);
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
 * @param boolean $captchaProtection
 * @param array $node
 * @return boolean
 * @access protected
 */
	protected function _captcha($continue, $captchaProtection, $node) {
		if (!empty($this->request->data) &&
			$captchaProtection &&
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
