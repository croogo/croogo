<?php
declare(strict_types=1);

namespace Croogo\Comments\View\Cell;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\View\Cell;
use Croogo\Comments\Model\Entity\Comment;
use Croogo\Taxonomy\Model\Entity\Type;

class CommentsCell extends Cell
{
    public function node($nodeId)
    {
        $this->loadModel('Croogo/Nodes.Nodes');

        $entity = $this->Nodes->get($nodeId, [
            'contain' => [
                'Comments' => function (Query $query) {
                    $query->find('threaded');

                    return $query;
                }
            ]
        ]);

        $this->set('entity', $entity);
    }

    public function commentFormNode(EntityInterface $entity, Type $type, Comment $comment = null, Comment $parentComment = null)
    {
        $this->loadModel('Croogo/Comments.Comments');

        $formUrl = [
            'plugin' => 'Croogo/Comments',
            'controller' => 'Comments',
            'action' => 'add',
            '?' => [
                'model' => 'Croogo/Nodes.Nodes',
                'foreign_key' => $entity->id,
                'parent_id' => $parentComment ? $parentComment->id : null,
            ],
        ];

        $this->set('title', $entity->title);
        $this->set('url', $entity->url);
        $this->set('formUrl', $formUrl);
        $this->set('entity', $entity);
        $this->set('comment', $comment ?: $this->Comments->newEntity([]));
        $this->set('parentComment', $parentComment);
        $this->set('captcha', $type->comment_captcha);
        $this->set('loggedInUser', $this->request->getSession()->read('Auth.User'));
    }
}
