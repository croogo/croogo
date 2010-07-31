<?php
/**
 * Comments Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CommentsController extends AppController {
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
        'Akismet',
        'Email',
        'Recaptcha',
    );
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    public $uses = array('Comment');

    public function beforeFilter() {
        parent::beforeFilter();
        if ($this->action == 'admin_edit') {
            $this->Security->disabledFields = array('ip');
        }
    }

    public function admin_index() {
        $this->set('title_for_layout', __('Comments', true));

        $this->Comment->recursive = 0;
        $this->paginate['Comment']['order'] = 'Comment.created DESC';
        $this->paginate['Comment']['conditions'] = array();
        $this->paginate['Comment']['conditions']['Comment.status'] = 1;
        $this->paginate['Comment']['comment_type'] = 'comment';

        if (isset($this->params['named']['filter'])) {
            $filters = $this->Croogo->extractFilter();
            foreach ($filters AS $filterKey => $filterValue) {
                if (strpos($filterKey, '.') === false) {
                    $filterKey = 'Comment.' . $filterKey;
                }
                $this->paginate['Comment']['conditions'][$filterKey] = $filterValue;
            }
        }

        if ($this->paginate['Comment']['conditions']['Comment.status'] == 1) {
            $this->set('title_for_layout', __('Comments: Published', true));
        } else {
            $this->set('title_for_layout', __('Comments: Approval', true));
        }

        $comments = $this->paginate();
        $this->set(compact('comments'));
    }

    public function admin_edit($id = null) {
        $this->set('title_for_layout', __('Edit Comment', true));

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Comment', true), 'default', array('class' => 'error'));
            $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->data)) {
            if ($this->Comment->save($this->data)) {
                $this->Session->setFlash(__('The Comment has been saved', true), 'default', array('class' => 'success'));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Comment could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Comment->read(null, $id);
        }
    }

    public function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Comment', true), 'default', array('class' => 'error'));
            $this->redirect(array('action'=>'index'));
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }
        if ($this->Comment->delete($id)) {
            $this->Session->setFlash(__('Comment deleted', true), 'default', array('class' => 'success'));
            $this->redirect(array('action'=>'index'));
        }
    }

    public function admin_process() {
        $action = $this->data['Comment']['action'];
        $ids = array();
        foreach ($this->data['Comment'] AS $id => $value) {
            if ($id != 'action' && $value['id'] == 1) {
                $ids[] = $id;
            }
        }

        if (count($ids) == 0 || $action == null) {
            $this->Session->setFlash(__('No items selected.', true), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
        }

        if ($action == 'delete' &&
            $this->Comment->deleteAll(array('Comment.id' => $ids), true, true)) {
            $this->Session->setFlash(__('Comments deleted.', true), 'default', array('class' => 'success'));
        } elseif ($action == 'publish' &&
            $this->Comment->updateAll(array('Comment.status' => 1), array('Comment.id' => $ids))) {
            $this->Session->setFlash(__('Comments published', true), 'default', array('class' => 'success'));
        } elseif ($action == 'unpublish' &&
            $this->Comment->updateAll(array('Comment.status' => 0), array('Comment.id' => $ids))) {
            $this->Session->setFlash(__('Comments unpublished', true), 'default', array('class' => 'success'));
        } else {
            $this->Session->setFlash(__('An error occurred.', true), 'default', array('class' => 'error'));
        }

        $this->redirect(array('action' => 'index'));
    }

    public function index() {
        $this->set('title_for_layout', __('Comments', true));

        if (!isset($this->params['url']['ext']) ||
            $this->params['url']['ext'] != 'rss') {
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

    public function add($nodeId = null, $parentId = null) {
        if (!$nodeId) {
            $this->Session->setFlash(__('Invalid Node', true), 'default', array('class' => 'error'));
            $this->redirect('/');
        }

        $node = $this->Comment->Node->find('first', array(
            'conditions' => array(
                'Node.id' => $nodeId,
                'Node.status' => 1,
            ),
        ));
        if (!isset($node['Node']['id'])) {
            $this->Session->setFlash(__('Invalid Node', true), 'default', array('class' => 'error'));
            $this->redirect('/');
        }
        if ($parentId) {
            $commentPath = $this->Comment->getpath($parentId, array('Comment.id'));
            $commentLevel = count($commentPath);
            if ($commentLevel > Configure::read('Comment.level')) {
                $this->Session->setFlash(__('Maximum level reached. You cannot reply to that comment.', true), 'default', array('class' => 'error'));
                $this->redirect($node['Node']['url']);
            }
        }
        $type = $this->Comment->Node->Taxonomy->Vocabulary->Type->findByAlias($node['Node']['type']);
        $continue = false;
        if ($type['Type']['comment_status'] && $node['Node']['comment_status']) {
            $continue = true;
        }

        // spam protection and captcha
        $continue = $this->__spam_protection($continue, $type, $node);
        $continue = $this->__captcha($continue, $type, $node);

        $success = 0;
        if (!empty($this->data) && $continue === true) {
            $data = array();
            if ($parentId &&
                $this->Comment->hasAny(array(
                    'Comment.id' => $parentId,
                    'Comment.node_id' => $nodeId,
                    'Comment.status' => 1,
                ))) {
                $data['parent_id'] = $parentId;
            }
            $data['node_id'] = $nodeId;
            if ($this->Session->check('Auth.User.id')) {
                $data['user_id'] = $this->Session->read('Auth.User.id');
                $data['name'] = $this->Session->read('Auth.User.name');
                $data['email'] = $this->Session->read('Auth.User.email');
                $data['website'] = $this->Session->read('Auth.User.website');
            } else {
                $data['name'] = htmlspecialchars($this->data['Comment']['name']);
                $data['email'] = $this->data['Comment']['email'];
                $data['website'] = $this->data['Comment']['website'];
            }
            $data['body'] = htmlspecialchars($this->data['Comment']['body']);
            $data['ip'] = $_SERVER['REMOTE_ADDR'];
            $data['type'] = $node['Node']['type'];
            if ($type['Type']['comment_approve']) {
                $data['status'] = 1;
            } else {
                $data['status'] = 0;
            }

            if ($this->Comment->save($data)) {
                $success = 1;
                if ($type['Type']['comment_approve']) {
                    $this->Session->setFlash(__('Your comment has been added successfully.', true), 'default', array('class' => 'success'));
                } else {
                    $this->Session->setFlash(__('Your comment will appear after moderation.', true), 'default', array('class' => 'success'));
                }

                // Email notification
                $this->Email->from = Configure::read('Site.title') . ' '
                    . '<croogo@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])).'>';
                $this->Email->to = Configure::read('Site.email');
                $this->Email->subject = '[' . Configure::read('Site.title') . '] '
                    . __('New comment posted under', true) . ' ' . $node['Node']['title'];
                $this->set('node', $node);
                $this->set('data', $data);
                $this->set('commentId', $this->Comment->id);
                $this->Email->template = 'comment';
                $this->Email->send();

                $this->redirect(Router::url($node['Node']['url'], true) . '#comment-' . $this->Comment->id);
            }
        }

        $this->set(compact('success', 'node', 'type', 'nodeId', 'parentId'));
    }

    private function __spam_protection($continue, $type, $node) {
        if (!empty($this->data) &&
            $type['Type']['comment_spam_protection'] &&
            $continue === true) {
            $this->Akismet->setCommentAuthor($this->data['Comment']['name']);
            $this->Akismet->setCommentAuthorEmail($this->data['Comment']['email']);
            $this->Akismet->setCommentAuthorURL($this->data['Comment']['website']);
            $this->Akismet->setCommentContent($this->data['Comment']['body']);
            //$this->Akismet->setPermalink(Router::url($node['Node']['url'], true));
            if ($this->Akismet->isCommentSpam()) {
                $continue = false;
                $this->Session->setFlash(__('Sorry, the comment appears to be spam.', true), 'default', array('class' => 'error'));
            }
        }

        return $continue;
    }

    private function __captcha($continue, $type, $node) {
        if (!empty($this->data) &&
            $type['Type']['comment_captcha'] &&
            $continue === true &&
            !$this->Recaptcha->valid($this->params['form'])) {
            $continue = false;
            $this->Session->setFlash(__('Invalid captcha entry', true), 'default', array('class' => 'error'));
        }
        
        return $continue;
    }

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
?>