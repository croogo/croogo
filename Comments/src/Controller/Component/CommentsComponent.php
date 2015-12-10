<?php

namespace Croogo\Comments\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;

/**
 * Comments Component
 *
 * @category Component
 * @package  Croogo.Comments.Controller.Component
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CommentsComponent extends Component
{

/**
 * Initialize
 */
    public function initialize(Event $event)
    {
        $this->_setupEvents($event);
    }

/**
 * Setup Event handlers
 *
 * @return void
 */
    protected function _setupEvents(Event $event)
    {
        $controller = $event->subject();
        $callback = [$this, 'getCommentData'];
        $eventManager = $controller->eventManager();
        $eventManager->attach($callback, 'Controller.Nodes.view');
    }

/**
 * Get comment data
 */
    public function getCommentData($event)
    {
        $controller = $event->subject;
        $alias = $controller->modelClass;
        $data = $event->data['data'];
        if ($data[$alias]['comment_count'] > 0) {
            $primaryKey = $controller->{$alias}->primaryKey;
            $comments = $controller->{$alias}->Comment->find('threaded', [
                'conditions' => [
                    'Comment.model' => $alias,
                    'Comment.foreign_key' => $data[$alias][$primaryKey],
                    'Comment.status' => 1,
                ],
                'contain' => [
                    'User',
                ],
                'cache' => [
                    'name' => 'comment_node_' . $data[$alias][$primaryKey],
                    'config' => 'nodes_view',
                ],
            ]);
        } else {
            $comments = [];
        }
        $controller->set(compact('comments'));
    }
}
