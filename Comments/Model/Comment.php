<?php

namespace Croogo\Comments\Model;
App::uses('AppModel', 'Model');
App::uses('CroogoStatus', 'Croogo.Lib');

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
class Comment extends AppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	public $name = 'Comment';

/**
 * @deprecated
 */
	const STATUS_APPROVED = 1;

/**
 * @deprecated
 */
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
		'Croogo.Publishable',
		'Croogo.Trackable',
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
 * Options:
 * - parentId id of parent comment (if it is a reply)
 * - userData author data (User data (if logged in) / Author fields from Comment form)
 *
 * @param array $data Comment data (Usually POSTed data from Comment form)
 * @param string $model Model alias
 * @param int $foreignKey Foreign Key (Node Id from where comment was posted).
 * @param array $options Options
 * @return bool true if comment was added, false otherwise.
 * @throws NotFoundException
 */
	public function add($data, $model, $foreignKey, $options = array()) {
		$options = Hash::merge(array(
			'parentId' => null,
			'userData' => array(),
		), $options);
		$record = array();
		$node = array();

		$foreignKey = (int)$foreignKey;
		$parentId = is_null($options['parentId']) ? null : (int)$options['parentId'];
		$userData = $options['userData'];

		if (empty($this->{$model})) {
			throw new UnexpectedValueException(sprintf('%s not configured for Comments', $model));
		}

		$node = $this->{$model}->findById($foreignKey);
		if (empty($node)) {
			throw new NotFoundException(__d('croogo', 'Invalid Node id'));
		}

		if (!is_null($parentId)) {
			if (
				$this->isValidLevel($parentId) &&
				$this->isApproved($parentId, $model, $foreignKey)
			) {
				$record['parent_id'] = $parentId;
			} else {
				return false;
			}
		}

		if (!empty($userData) && is_array($userData)) {
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
		$record['model'] = $model;
		$record['foreign_key'] = $node[$this->{$model}->alias]['id'];
		$record['body'] = h($data[$this->alias]['body']);

		if (isset($node[$this->{$model}->alias]['type'])) {
			$record['type'] = $node[$this->{$model}->alias]['type'];
		} else {
			$record['type'] = '';
		}

		if (isset($data[$this->alias]['status'])) {
			$record['status'] = $data[$this->alias]['status'];
		} else {
			$record['status'] = CroogoStatus::PENDING;
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
	public function isApproved($commentId, $model, $foreignKey) {
		return $this->hasAny(array(
			$this->escapeField() => $commentId,
			$this->escapeField('model') => $model,
			$this->escapeField('foreign_key') => $foreignKey,
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

/**
 * Change status of given Comment Ids
 *
 * @param array $ids array of Comment Ids
 * @param boolean
 * @return mixed
 * @see Model::saveMany()
 */
	public function changeStatus($ids, $status) {
		$dataArray = array();
		foreach ($ids as $id) {
			$dataArray[] = array(
				$this->primaryKey => $id,
				'status' => $status
			);
		}
		return $this->saveMany($dataArray, array('validate' => false));
	}

/**
 * Provide our own bulkPublish since BulkProcessBehavior::bulkPublish is incompatible with boolean status
 */
	public function bulkPublish($ids) {
		return $this->changeStatus($ids, true);
	}

/**
 * Provide our own bulkUnpublish since BulkProcessBehavior::bulkUnpublish is incompatible with boolean status
 */
	public function bulkUnpublish($ids) {
		return $this->changeStatus($ids, false);
	}

}
