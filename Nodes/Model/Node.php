<?php

App::uses('NodesAppModel', 'Nodes.Model');

/**
 * Node
 *
 * @category Nodes.Model
 * @package  Croogo.Nodes.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Node extends NodesAppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	public $name = 'Node';

	const DEFAULT_TYPE = 'node';

/**
 * Publish status
 *
 * @see PublishableBehavior
 * @deprecated Use CroogoStatus::PUBLISHED
 */
	const STATUS_PUBLISHED = 1;

/**
 * Unpublish status
 *
 * @see PublishableBehavior
 * @deprecated Use CroogoStatus::UNPUBLISHED
 */
	const STATUS_UNPUBLISHED = 0;

	const STATUS_PROMOTED = 1;

	const STATUS_UNPROMOTED = 0;

	const PUBLICATION_STATE_FIELD = 'status';

	const PROMOTION_STATE_FIELD = 'promote';

	const UNPROCESSED_ACTION = 'delete';

	public $actionsMapping = array(
		'delete' => 'deleteAll',
		'publish' => '_publish',
		'promote' => '_promote',
		'unpublish' => '_unpublish',
		'unpromote' => '_unpromote',
	);

/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
	public $actsAs = array(
		'Tree',
		'Croogo.Encoder',
		'Croogo.Publishable',
		'Croogo.Trackable',
		'Meta.Meta',
		'Croogo.Url',
		'Croogo.Cached' => array(
			'groups' => array(
				'nodes',
			),
		),
		'Search.Searchable',
	);

/**
 * Node type
 *
 * If the Model is associated to Node model, this variable holds the Node type value
 *
 * @var string
 * @access public
 */
	public $type = null;

/**
 * Guid
 *
 * @var string
 * @access public
 */
	public $guid = null;

/**
 * Validation
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'title' => array(
			'rule' => 'notEmpty',
			'message' => 'This field cannot be left blank.',
		),
		'slug' => array(
			'isUniquePerType' => array(
				'rule' => 'isUniquePerType',
				'message' => 'This slug has already been taken.',
			),
			'minLength' => array(
				'rule' => array('minLength', 1),
				'message' => 'Slug cannot be empty.',
			),
		),
	);

/**
 * Filter search fields
 *
 * @var array
 * @access public
 */
	public $filterArgs = array(
		'filter' => array('type' => 'query', 'method' => 'filterNodes'),
		'type' => array('type' => 'value'),
		'status' => array('type' => 'value'),
		'promote' => array('type' => 'value'),
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
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
		),
	);

	public $findMethods = array(
		'promoted' => true,
		'viewBySlug' => true,
		'viewById' => true,
	);

/**
 * beforeFind callback
 *
 * @param array $q
 * @return array
 */
	public function beforeFind($queryData) {
		$typeField = $this->alias . '.type';
		if ($this->type != null && !isset($queryData['conditions'][$typeField])) {
			$queryData['conditions'][$typeField] = $this->type;
		}
		return $queryData;
	}

/**
 * beforeSave callback
 *
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if (empty($this->data[$this->alias]['type']) && $this->type != null) {
			$this->data[$this->alias]['type'] = $this->type;
		}

		$dateFields = array('created');
		foreach ($dateFields as $dateField) {
			if (!array_key_exists($dateField, $this->data[$this->alias])) {
				continue;
			}
			if (empty($this->data[$this->alias][$dateField])) {
				$db = $this->getDataSource();
				$colType = array_merge(array(
					'formatter' => 'date',
					), $db->columns[$this->getColumnType($dateField)]
				);
				$this->data[$this->alias][$dateField] = call_user_func(
					$colType['formatter'], $colType['format']
				);
			}
		}

		return true;
	}

/**
 * Returns false if any fields passed match any (by default, all if $or = false) of their matching values.
 *
 * @param array $fields Field/value pairs to search (if no values specified, they are pulled from $this->data)
 * @param boolean $or If false, all fields specified must match in order for a false return value
 * @return boolean False if any records matching any fields are found
 * @access public
 */
	public function isUniquePerType($fields, $or = true) {
		if (!is_array($fields)) {
			$fields = func_get_args();
			if (is_bool($fields[count($fields) - 1])) {
				$or = $fields[count($fields) - 1];
				unset($fields[count($fields) - 1]);
			}
		}

		foreach ($fields as $field => $value) {
			if (is_numeric($field)) {
				unset($fields[$field]);

				$field = $value;
				if (isset($this->data[$this->alias][$field])) {
					$value = $this->data[$this->alias][$field];
				} else {
					$value = null;
				}
			}

			if (strpos($field, '.') === false) {
				unset($fields[$field]);
				$fields[$this->alias . '.' . $field] = $value;
			}
		}
		if ($or) {
			$fields = array('or' => $fields);
		}
		if (!empty($this->id)) {
			$fields[$this->alias . '.' . $this->primaryKey . ' !='] = $this->id;
		}
		if (!empty($this->type)) {
			$fields[$this->alias . '.type'] = $this->type;
		}
		return ($this->find('count', array('conditions' => $fields, 'recursive' => -1)) == 0);
	}

/**
 * Return filter condition for Nodes
 *
 * @return array Array of conditions
 */
	public function filterNodes($data = array()) {
		$conditions = array();
		if (!empty($data['filter'])) {
			$filter = '%' . $data['filter'] . '%';
			$conditions = array(
				'OR' => array(
					$this->alias . '.title LIKE' => $filter,
					$this->alias . '.excerpt LIKE' => $filter,
					$this->alias . '.body LIKE' => $filter,
					$this->alias . '.terms LIKE' => $filter,
				),
			);
		}
		return $conditions;
	}

/**
 * Create/update a Node record
 *
 * @param $data array Node data
 * @param $typeAlias string Node type alias
 * @return mixed see Model::saveAll()
 */
	public function saveNode($data, $typeAlias = self::DEFAULT_TYPE) {
		$result = false;

		$data = $this->formatData($data, $typeAlias);
		$event = Croogo::dispatchEvent('Model.Node.beforeSaveNode', $this, compact('data', 'typeAlias'));
		$result = $this->saveAll($event->data['data']);
		Croogo::dispatchEvent('Model.Node.afterSaveNode', $this, $event->data);

		return $result;
	}

/**
 * Process action pass as argument
 *
 * @param $action string actionToPerfom
 * @param $ids array nodes ids to perform action upon
 * @throws InvalidArgumentException
 */
	public function processAction($action, $ids) {
		$success = true;
		$actionToPerform = strtolower($action);

		if (!in_array($actionToPerform, array_keys($this->actionsMapping))) {
			throw new InvalidArgumentException(__d('croogo', 'Invalid action to perform'));
		}

		if (empty($ids)) {
			throw new InvalidArgumentException(__d('croogo', 'No target to process action upon'));
		}

		if ($actionToPerform === self::UNPROCESSED_ACTION) {
			$success = $this->{$this->actionsMapping[$actionToPerform]}(array($this->escapeField() => $ids), true, true);
		} else {
			$success = $this->{$this->actionsMapping[$actionToPerform]}($ids);
		}

		return $success;
	}

/**
 * Format data for saving
 *
 * @param array $data Node and related data, eg Taxonomy and Role
 * @param string $typeAlias string Node type alias
 * @return array formatted data
 * @throws InvalidArgumentException
 */
	public function formatData($data, $typeAlias = self::DEFAULT_TYPE) {
		$roles = $type = array();

		if (!array_key_exists($this->alias, $data)) {
			$data = array($this->alias => $data);
		} else {
			$data = $data;
		}

		$data[$this->alias]['path'] = $this->_getNodeRelativePath($data);

		if (!array_key_exists('Role', $data) || empty($data['Role']['Role'])) {
			$roles = '';
		} else {
			$roles = $data['Role']['Role'];
		}

		$data[$this->alias]['visibility_roles'] = $this->encodeData($roles);

		return $data;
	}

/**
 * Update values for all nodes 'path' field
 *
 * @return bool|array Depending on atomicity
 * @see Model::saveMany()
 */
	public function updateAllNodesPaths() {
		$types = $this->Taxonomy->Vocabulary->Type->find('list', array(
			'fields' => array(
				'Type.id',
				'Type.alias',
			),
		));
		$typesAlias = array_values($types);

		$nodes = $this->find('all', array(
			'conditions' => array(
				$this->alias . '.type' => $typesAlias,
			),
			'fields' => array(
				$this->alias . '.id',
				$this->alias . '.slug',
				$this->alias . '.type',
				$this->alias . '.path',
			),
			'recursive' => '-1',
		));
		foreach ($nodes as &$node) {
			$node[$this->alias]['path'] = $this->_getNodeRelativePath($node);
		}

		return $this->saveMany($nodes, array('fieldList' => array('path')));
	}

/**
 * getNodeRelativePath
 *
 * @param array $node Node array
 * @return string relative node path
 */
	protected function _getNodeRelativePath($node) {
		return Croogo::getRelativePath(array(
			'plugin' => 'nodes',
			'admin' => false,
			'controller' => 'nodes',
			'action' => 'view',
			'type' => $this->_getType($node),
			'slug' => $node[$this->alias]['slug'],
		));
	}

/**
 * _getType
 *
 * @param array $data Node data
 * @return string type
 */
	protected function _getType($data) {
		if (empty($data[$this->alias]['type'])) {
			$type = is_null($this->type) ? self::DEFAULT_TYPE : $this->type;
		} else {
			$type = $data[$this->alias]['type'];
		}

		return $type;
	}

/**
 * Find promoted nodes
 *
 * @see Model::find()
 * @see Model::_findAll()
 */
	protected function _findPromoted($state, $query, $results = array()) {
		if ($state === 'before') {
			$_defaultFilters = array('contain', 'limit', 'order', 'conditions');
			$_defaultContain = array(
				'Meta',
				'Taxonomy' => array(
					'Term',
					'Vocabulary',
				),
				'User',
			);
			$_defaultConditions = array(
				'Node.status' => $this->status(),
				'Node.promote' => self::STATUS_PROMOTED,
				'OR' => array(
					'Node.visibility_roles' => '',
				),
			);
			$_defaultOrder = $this->alias . '.created DESC';
			$_defaultLimit = Configure::read('Reading.nodes_per_page');

			foreach ($_defaultFilters as $filter) {
				$this->_mergeQueryFilters($query, $filter, ${'_default' . ucfirst($filter)});
			}

			return $query;
		} else {
			return $results;
		}
	}

/**
 * Find a single node by id
 */
	protected function _findViewById($state, $query, $results = array()) {
		if ($state == 'after') {
			if (isset($results[0])) {
				return $results[0];
			}
			return $results;
		}

		if ($query['conditions'] === null) {
			$query = Hash::merge($query, array(
				'conditions' => array(),
			));
		}

		$keys = array('id' => null, 'roleId' => null);
		$args = array_merge($keys, array_intersect_key($query, $keys));
		$query = array_diff_key($query, $args);
		$query = Hash::merge(array(
			'conditions' => array(
				'Node.id' => $args['id'],
				'Node.status' => $this->status(),
				'OR' => array(
					'Node.visibility_roles' => '',
					'Node.visibility_roles LIKE' => '%"' . $args['roleId'] . '"%',
				),
			),
			'contain' => array(
				'Meta',
				'Taxonomy' => array(
					'Term',
					'Vocabulary',
				),
				'User',
			),
			'cache' => array(
				'name' => 'node_' . $args['roleId'] . '_' . $args['id'],
				'config' => 'nodes_view',
			),
		), $query);

		return $query;
	}

/**
 * Find a single node by slug
 */
	protected function _findViewBySlug($state, $query, $results = array()) {
		if ($state == 'after') {
			if (isset($results[0])) {
				return $results[0];
			}
			return $results;
		}

		if ($query['conditions'] === null) {
			$query = Hash::merge($query, array(
				'conditions' => array(),
			));
		}

		$keys = array('slug' => null, 'type' => null, 'roleId' => null);
		$args = array_merge($keys, array_intersect_key($query, $keys));
		$query = array_diff_key($query, $args);
		$query = Hash::merge(array(
			'conditions' => array(
				'Node.slug' => $args['slug'],
				'Node.type' => $args['type'],
				'Node.status' => $this->status(),
				'OR' => array(
					'Node.visibility_roles' => '',
					'Node.visibility_roles LIKE' => '%"' . $args['roleId'] . '"%',
				),
			),
			'contain' => array(
				'Meta',
				'Taxonomy' => array(
					'Term',
					'Vocabulary',
				),
				'User',
			),
			'cache' => array(
				'name' => 'node_' . $args['roleId'] . '_' . $args['type'] . '_' . $args['slug'],
				'config' => 'nodes_view',
			),
		), $query);

		return $query;
	}

/**
 * mergeQueryFilters
 *
 * @see Node::_findPromoted()
 * @return void
 */
	protected function _mergeQueryFilters(&$query, $key, $values) {
		if (!empty($query[$key])) {
			if (is_array($query[$key])) {
				$query[$key] = Hash::merge($query[$key], $values);
			}
		} else {
			$query[$key] = $values;
		}
	}

/**
 * Internal helper function to change state fields
 * @see Node::processAction()
 */
	protected function _publish($ids) {
		return $this->_saveStatus($ids, self::PUBLICATION_STATE_FIELD, self::STATUS_PUBLISHED);
	}

/**
 * Internal helper function to change state fields
 * @see Node::processAction()
 */
	protected function _unpublish($ids) {
		return $this->_saveStatus($ids, self::PUBLICATION_STATE_FIELD, self::STATUS_UNPUBLISHED);
	}

/**
 * Internal helper function to change state fields
 * @see Node::processAction()
 */
	protected function _promote($ids) {
		return $this->_saveStatus($ids, self::PROMOTION_STATE_FIELD, self::STATUS_PROMOTED);
	}

/**
 * Internal helper function to change state fields
 * @see Node::processAction()
 */
	protected function _unpromote($ids) {
		return $this->_saveStatus($ids, self::PROMOTION_STATE_FIELD, self::STATUS_UNPROMOTED);
	}

/**
 * Internal helper function to change state fields
 * @see Node::processAction()
 */
	protected function _saveStatus($ids, $field, $status) {
		return $this->updateAll(array($this->escapeField($field) => $status), array($this->escapeField() => $ids));
	}

}
