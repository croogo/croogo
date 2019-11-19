<?php

namespace Croogo\Comments\Model\Table;

use Cake\Core\Configure;
use Cake\Mailer\MailerAwareTrait;
use Cake\Network\Exception\NotFoundException;
use Cake\Utility\Hash;
use Cake\Validation\Validator;
use Croogo\Comments\Model\Entity\Comment;
use Croogo\Core\Model\Table\CroogoTable;
use Croogo\Core\Status;
use UnexpectedValueException;

/**
 * Comment
 *
 * @category Model
 * @package  Croogo.Comments.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CommentsTable extends CroogoTable
{
    use MailerAwareTrait;

    /**
     * Display fields for this model
     *
     * @var array
     */
    protected $_displayFields = [
        'id',
        'name',
        'email',
        'website',
        'title',
        'body',
        'type',
    ];

    public function initialize(array $config)
    {
        $this->setEntityClass('Croogo/Comments.Comment');

        $this->belongsTo('Users', [
            'className' => 'Croogo/Users.Users',
            'foreignKey' => 'user_id',
        ]);
        $this->addBehavior('Croogo/Core.Publishable');
        $this->addBehavior('Croogo/Core.Trackable');
        $this->addBehavior('Croogo/Core.LinkedModel');
        $this->addBehavior('Search.Search');
        $this->addBehavior('Tree');
        $this->addBehavior('Croogo/Core.Cached', [
            'groups' => ['comments', 'nodes']
        ]);
        $this->addBehavior('Timestamp');

        $this->searchManager()
            ->add('status', 'Search.Value', [
                'field' => 'status'
            ]);

        $this->getEventManager()->on($this->getMailer('Croogo/Comments.Comment'));
    }

    /**
     * @param \Cake\Validation\Validator $validator Validator object
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        return $validator
            ->notBlank('body', 'You must write a comment.')

            ->notBlank('name', 'You must supply a name.')

            ->notBlank('email', 'Please supply an email address.')
            ->email('email', false, 'Please supply a valid email address.');
    }

    /**
     * Add a new Comment
     *
     * Options:
     * - parentId id of parent comment (if it is a reply)
     * - userData author data (User data (if logged in) / Author fields from Comment form)
     *
     * @param array $comment Comment data (Usually POSTed data from Comment form)
     * @param string $model Model alias
     * @param int $foreignKey Foreign Key (Node Id from where comment was posted).
     * @param array $options Options
     * @return bool true if comment was added, false otherwise.
     * @throws NotFoundException
     */
    public function add(Comment $comment, $model, $foreignKey, $options = [])
    {
        $options = Hash::merge([
            'parentId' => null,
            'userData' => [],
        ], $options);

        list($plugin, $modelAlias) = pluginSplit($model);

        $foreignKey = (int)$foreignKey;
        $parentId = is_null($options['parentId']) ? null : (int)$options['parentId'];
        $userData = $options['userData'];

        if (empty($this->{$modelAlias})) {
            throw new UnexpectedValueException(sprintf('%s not configured for Comments', $model));
        }

        $entity = $this->{$modelAlias}->findById($foreignKey)->firstOrFail();

        if (!is_null($parentId)) {
            if ($this->isValidLevel($parentId) &&
                $this->isApproved($parentId, $model, $foreignKey)
            ) {
                $comment->parent_id = $parentId;
            } else {
                return false;
            }
        }

        if (!empty($userData)) {
            $comment->user_id = $userData['User']['id'];
            $comment->name = $userData['User']['name'];
            $comment->email = $userData['User']['email'];
            $comment->website = $userData['User']['website'];
        }

        $comment->model = $model;
        $comment->foreign_key = $entity->id;

        if ($entity->has('type')) {
            $comment['type'] = $entity->type;
        }

        if ($comment->status === null) {
            $comment->status = Status::PENDING;
        }

        return (bool)$this->save($comment);
    }

    /**
     * Checks wether comment has been approved
     *
     * @param int $commentId comment id
     * @param int $model node id
     * @return bool true if comment is approved
     */
    public function isApproved($commentId, $model, $foreignKey)
    {
        return $this->exists([
            $this->aliasField('id') => $commentId,
            $this->aliasField('model') => $model,
            $this->aliasField('foreign_key') => $foreignKey,
            $this->aliasField('status') => 1,
        ]);
    }

    /**
     * Checks wether comment is within valid level range
     *
     * @return bool
     * @throws NotFoundException
     */
    public function isValidLevel($commentId)
    {
        if (!$this->exists(['id' => $commentId])) {
            throw new NotFoundException(__d('croogo', 'Invalid Comment id'));
        }

        $level = $this->find('path', ['for' => $commentId])
            ->select([$this->aliasField($this->primaryKey())])
            ->count();

        return Configure::read('Comment.level') > $level;
    }

    /**
     * Change status of given Comment Ids
     *
     * @param array $ids array of Comment Ids
     * @param bool
     * @return mixed
     * @see Model::saveMany()
     */
    public function changeStatus($ids, $status)
    {
        return $this->updateAll([
            'status' => $status,
        ], [
            $this->primaryKey() . ' IN' => $ids
        ]);
    }

    /**
     * Provide our own bulkPublish since BulkProcessBehavior::bulkPublish is incompatible with boolean status
     */
    public function bulkPublish($ids)
    {
        return $this->changeStatus($ids, true);
    }

    /**
     * Provide our own bulkUnpublish since BulkProcessBehavior::bulkUnpublish is incompatible with boolean status
     */
    public function bulkUnpublish($ids)
    {
        return $this->changeStatus($ids, false);
    }
}
