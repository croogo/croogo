<?php

namespace Croogo\Comments\Controller;

use App\Network\Email\Email;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Routing\Router;
use Croogo\Comments\Model\Entity\Comment;
use Croogo\Core\Status;
use UnexpectedValueException;

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
class CommentsController extends AppController
{

/**
 * Preset Variable Search
 * @var array
 */
    public $presetVars = true;

    public function initialize()
    {
        parent::initialize();

        $this->_loadCroogoComponents(['Akismet', 'BulkProcess', 'Recaptcha' => [
            'actions' => ['add']
        ]]);
        $this->_setupPrg();
    }

/**
 * index
 *
 * @return void
 * @access public
 */
    public function index()
    {
        $this->set('title_for_layout', __d('croogo', 'Comments'));

        if (!isset($this->request['_ext']) ||
            $this->request['_ext'] != 'rss') {
            return $this->redirect('/');
        }

        $roleId = $this->Croogo->roleId();
        $this->paginate = [
            'contain' => ['Nodes', 'Users'],
            'conditions' => [
                $this->Comments->aliasField('status') .' IN' => $this->Comments->status($roleId, 'approval'),
            ],
            'order' => [
                'weight' => 'DESC',
            ],
            'limit' => Configure::read('Comment.feed_limit')
        ];

        $this->set('comments', $this->paginate());
    }

/**
 * add
 *
 * @param int $foreignKey
 * @param int $parentId
 * @return \Cake\Network\Response|null
 * @access public
 * @throws UnexpectedValueException
 */
    public function add($model, $foreignKey = null, $parentId = null)
    {
        if (!$foreignKey) {
            $this->Flash->error(__d('croogo', 'Invalid id'));
            return $this->redirect('/');
        }

        list($plugin, $modelAlias) = pluginSplit($model);

        if (empty($this->Comments->{$modelAlias})) {
            throw new UnexpectedValueException(
                sprintf('%s not configured for Comments', $modelAlias)
            );
        }

        $roleId = $this->Croogo->roleId();
        $Model = $this->Comments->{$modelAlias};
        $entity = $Model->find()->where([
            $Model->aliasField($Model->primaryKey()) => $foreignKey,
            $Model->aliasField('status') . ' IN' => $Model->status($roleId, 'approval'),
        ])->first();

        if (isset($entity->path)) {
            $redirectUrl = $entity->path;
        } else {
            $redirectUrl = $this->referer();
        }

        if (!is_null($parentId) && !$this->Comments->isValidLevel($parentId)) {
            $this->Flash->error(__d('croogo', 'Maximum level reached. You cannot reply to that comment.'));
            return $this->redirect($redirectUrl);
        }

        $typeSetting = $Model->getTypeSetting($entity);
        extract(array_intersect_key($typeSetting, [
            'commentable' => null,
            'autoApprove' => null,
            'spamProtection' => null,
            'captchaProtection' => null,
            ]));
        $continue = $commentable && $entity->comment_status;

        if (!$continue) {
            $this->Flash->error(__d('croogo', 'Comments are not allowed.'));
            return $this->redirect($redirectUrl);
        }

        // spam protection and captcha
        $continue = $this->_spamProtection($continue, $spamProtection, $entity);
        $continue = $this->_captcha($continue, $captchaProtection, $entity);
        $success = false;
        $comment = null;
        if (!empty($this->request->data) && $continue === true) {
            $comment = $this->Comments->newEntity($this->request->data);
            $comment->ip = $this->request->clientIp();
            $comment->status = $autoApprove ? Status::APPROVED : Status::PENDING;

            $userData = [];
            if ($this->Auth->user()) {
                $userData['User'] = $this->Auth->user();
            }

            $options = [
                'parentId' => $parentId,
                'userData' => $userData,
            ];
            $success = $this->Comments->add($comment, $model, $foreignKey, $options);
            if ($success) {
                if ($autoApprove) {
                    $messageFlash = __d('croogo', 'Your comment has been added successfully.');
                } else {
                    $messageFlash = __d('croogo', 'Your comment will appear after moderation.');
                }
                $this->Flash->success($messageFlash);

                return $this->redirect(Router::url($entity->url->getUrl()) . '#comment-' . $comment->id);
            }
        }

        $this->set(compact('success', 'entity', 'type', 'model', 'foreignKey', 'parentId', 'comment'));
    }

/**
 * Spam Protection
 *
 * @param bool $continue
 * @param bool $spamProtection
 * @param array $node
 * @return boolean
 * @access protected
 * @deprecated This method will be renamed to _spamProtection() in the future
 */
    protected function _spamProtection($continue, $spamProtection, $node)
    {
        if (!empty($this->request->data) &&
            $spamProtection &&
            $continue === true) {
            $this->Akismet->setCommentAuthor($this->request->data['Comment']['name']);
            $this->Akismet->setCommentAuthorEmail($this->request->data['Comment']['email']);
            $this->Akismet->setCommentAuthorURL($this->request->data['Comment']['website']);
            $this->Akismet->setCommentContent($this->request->data['Comment']['body']);
            if ($this->Akismet->isCommentSpam()) {
                $continue = false;
                $this->Flash->error(__d('croogo', 'Sorry, the comment appears to be spam.'));
            }
        }

        return $continue;
    }

/**
 * Captcha
 *
 * @param bool $continue
 * @param bool $captchaProtection
 * @param array $node
 * @return boolean
 * @access protected
 */
    protected function _captcha($continue, $captchaProtection, $node)
    {
        if (!empty($this->request->data) &&
            $captchaProtection &&
            $continue === true &&
            !$this->Recaptcha->verify($this->request)) {
            $continue = false;
            $this->Flash->error(__d('croogo', 'Sorry, the comment did not pass the security challenge'));
        }

        return $continue;
    }

/**
 * delete
 *
 * @param int $id
 * @return void
 * @access public
 */
    public function delete($id)
    {
        $success = 0;
        if ($this->Session->check('Auth.User.id')) {
            $userId = $this->Session->read('Auth.User.id');
            $comment = $this->Comment->find('first', [
                'conditions' => [
                    'Comment.id' => $id,
                    'Comment.user_id' => $userId,
                ],
            ]);

            if (isset($comment['Comment']['id']) &&
                $this->Comment->delete($id)) {
                $success = 1;
            }
        }

        $this->set(compact('success'));
    }
}
