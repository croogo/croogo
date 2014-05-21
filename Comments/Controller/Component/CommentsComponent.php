<?php

namespace Croogo\Comments\Controller\Component;

use Cake\Controller\Component;
/**
 * Comments Component
 *
 * @category Component
 * @package  Croogo.Comments.Controller.Component
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CommentsComponent extends Component {

/**
 * Initialize
 */
	public function initialize(Controller $controller) {
		$this->_setupEvents($controller);
	}

/**
 * Setup Event handlers
 *
 * @return void
 */
	protected function _setupEvents(Controller $controller) {
		$callback = array($this, 'getCommentData');
		$eventManager = $controller->getEventManager();
		$eventManager->attach($callback, 'Controller.Nodes.view');
	}

/**
 * Get comment data
 */
	public function getCommentData($event) {
		$controller = $event->subject;
		$alias = $controller->modelClass;
		$data = $event->data['data'];
		if ($data[$alias]['comment_count'] > 0) {
			$primaryKey = $controller->{$alias}->primaryKey;
			$comments = $controller->{$alias}->Comment->find('threaded', array(
				'conditions' => array(
					'Comment.model' => $alias,
					'Comment.foreign_key' => $data[$alias][$primaryKey],
					'Comment.status' => 1,
				),
				'contain' => array(
					'User',
				),
				'cache' => array(
					'name' => 'comment_node_' . $data[$alias][$primaryKey],
					'config' => 'nodes_view',
				),
			));
		} else {
			$comments = array();
		}
		$controller->set(compact('comments'));
	}

}
