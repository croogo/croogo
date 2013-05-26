<?php

App::uses('AppModel', 'Model');

/**
 * Comment
 *
 * PHP version 5
 *
 * @category Model
 * @package  Croogo.Comments.Model
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

	const STATUS_PENDING = 0;

/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
	public $actsAs = array(
		'Tree',
		'Croogo.Cached' => array(
			'groups' => array(
				'comments',
				'nodes',
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
		),
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'This field cannot be left blank.',
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
			'counterScope' => array('Comment.status' => self::STATUS_APPROVED),
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
 * @param int   $nodeId Node Id (Node Id from where comment was posted).
 * @param array $nodeType Type data (Node's Type data)
 * @param mixed int/null $parentId id of parent comment (if it is a reply)
 * @param array $userData author data (User data (if logged in) / Author fields from Comment form)
 *
 * @return bool true if comment was added, false otherwise.
 * @throws NotFoundException
 */
	public function add($data, $nodeId, $nodeType, $parentId = null, $userData = array()) {
		$record = array();
		$node = array();

		$nodeId = (int)$nodeId;
		$parentId = is_null($parentId) ? null : (int)$parentId;

		$node = $this->Node->findById($nodeId);
		if (empty($node)) {
			throw new NotFoundException(__d('croogo', 'Invalid Node id'));
		}

		if (!is_null($parentId)) {
			if ($this->isValidLevel($parentId) && $this->isApproved($parentId, $nodeId)) {
				$record['parent_id'] = $parentId;
			} else {
				return false;
			}
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
			$record['status'] = self::STATUS_PENDING;
		}

		return (bool)$this->save($record);
	}

/**
 * Checks wether comment has been approved
 *
 * @param integer $commentId comment id
 * @param integer $nodeId node id
 * @return boolean true if comment is approved
 */
	public function isApproved($commentId, $nodeId) {
		return $this->hasAny(array(
			$this->escapeField() => $commentId,
			$this->escapeField('node_id') => $nodeId,
			$this->escapeField('status') => 1,
		));
	}

/**
 * Checks wether comment is within valid level range
 *
 * @return boolean
 * @throws NotFoundException
 */
	public function isValidLevel($commentId) {
		if (!$this->exists($commentId)) {
			throw new NotFoundException(__d('croogo', 'Invalid Comment id'));
		}

		$path = $this->getPath($commentId, array($this->escapeField()));
		$level = count($path);

		return Configure::read('Comment.level') > $level;
	}

}
