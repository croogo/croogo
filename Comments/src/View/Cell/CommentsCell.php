<?php

namespace Croogo\Comments\View\Cell;

use Cake\ORM\Query;
use Cake\View\Cell;
use Croogo\Nodes\Model\Entity\Node;
use Croogo\Taxonomy\Model\Entity\Type;

class CommentsCell extends Cell
{
    public function node($nodeId)
    {
        $this->loadModel('Croogo/Nodes.Nodes');

        $node = $this->Nodes->get($nodeId, [
            'contain' => [
                'Comments' => function (Query $query) {
                    $query->find('threaded');

                    return $query;
                }
            ]
        ]);

        $this->set('node', $node);
    }

    public function commentFormNode(Node $node, Type $type)
    {
        $this->loadModel('Croogo/Commments.Comments');

        $formUrl = [
            'plugin' => 'Croogo/Comments',
            'controller' => 'Comments',
            'action' => 'add',
            'Nodes',
            $node->id,
        ];

        $this->set('title', $node->title);
        $this->set('url', $node->url);
        $this->set('formUrl', $formUrl);
        $this->set('comment', $this->Comments->newEntity());
        $this->set('captcha', $type->comment_captcha);
        $this->set('loggedInUser', $this->request->session()->read('Auth.User'));
    }
}
