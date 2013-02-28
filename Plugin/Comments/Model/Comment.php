<?php

App::uses('AppModel', 'Model');

/**
 * Comment
 *
 * PHP version 5
 *
 * @category Model
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Comment extends AppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	public $name = 'Comment';

	const STATUS_APPROVED = 1;
	const STATUS_MODERATED = 0;

/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
	public $actsAs = array(
		'Tree',
		'Cached' => array(
			'prefix' => array(
				'comment_',
				'nodes_',
				'node_',
			),
		),
		'Search.Searchable',
	);

/**
 * Validation
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'body' => array(
			'rule' => 'notEmpty',
			'message' => 'This field cannot be left blank.',
			'required' => true,
		),
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'This field cannot be left blank.',
			'required' => true,
		),
		'email' => array(
			'rule' => 'email',
			'required' => true,
			'message' => 'Please enter a valid email address.',
		),
	);

/**
 * Model associations: belongsTo
 *
 * @var array
 * @access public
 */
	public $belongsTo = array(
		'Node' => array(
			'className' => 'Nodes.Node',
			'counterCache' => true,
			'counterScope' => array('Comment.status' => 1),
		),
		'User' => array(
			'className' => 'Users.User',
		),
	);

/**
 * Filter fields
 *
 * @var array
 */
	public $filterArgs = array(
		'status' => array('type' => 'value'),
	);

/**
 * Add a new Comment
 *
 * @param array $data Comment data (Usually POSTed data from Comment form)
 * @param int 	$nodeId Node Id (Node Id from where comment was posted).
 * @param array $nodeType Type data (Node's Type data)
 * @param mixed int/null $parentId id of parent comment (if it is a reply)
 * @param array $userData author data (User data (if logged in) / Author fields from Comment form)
 *
 * @return bool true if comment was added, false otherwise.
 */

	public function add($data, $nodeId, $nodeType, $parentId = null, $userData = array()) {
		$record = array();
		$node = array();

		$node = $this->Node->findById($nodeId);
		if (empty($node)) {
			throw new NotFoundException(__d('comments', 'Invalid Node id'));
		}

		if (
			!is_null($parentId) &&
			$this->isAllowToCommentOnParent($parentId) &&
			$this->parentCommentIsApproved($parentId, $nodeId)
		) {
			$record['parent_id'] = $parentId;
		}

		if (!empty($userData)) {
			$record['user_id'] = $userData['User']['id'];
			$record['name'] = $userData['User']['name'];
			$record['email'] = $userData['User']['email'];
			$record['website'] = $userData['User']['website'];
		} else {
			$record['name'] = $data[$this->alias]['name'];
			$record['email'] = $data[$this->alias]['email'];
			$record['website'] = $data[$this->alias]['website'];
		}

		$record['ip'] = $data[$this->alias]['ip'];
		$record['node_id'] = $node['Node']['id'];
		$record['body'] = h($data[$this->alias]['body']);
		$record['type'] = $nodeType['Type']['alias'];

		if ($nodeType['Type']['comment_approve']) {
			$record['status'] = self::STATUS_APPROVED;
		} else {
			$record['status'] = self::STATUS_MODERATED;
		}

		return (bool) $this->save($record);
	}

	public function parentCommentIsApproved($parentId, $nodeId) {
		return $this->hasAny(array(
			$this->escapeField() => $parentId,
			$this->escapeField('node_id') => $nodeId,
			$this->escapeField('status') => 1,
		));
	}

	public function isAllowToCommentOnParent($parentId){
		if (is_null($parentId) || !$this->exists($parentId)) {
			throw new NotFoundException(__d('comments', 'Invalid Comment id'));
		}

		$parentId;

		$path = $this->getPath($parentId, array($this->escapeField()));
		$level = count($path);

		return Configure::read('Comment.level') > $level;
	}

}
