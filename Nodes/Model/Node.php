<?php

namespace Croogo\Nodes\Model;
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

/**
 * @deprecated Use CroogoStatus::PROMOTED
 */
	const STATUS_PROMOTED = 1;

/**
 * @deprecated Use CroogoStatus::UNPROMOTED
 */
	const STATUS_UNPROMOTED = 0;

/**
 * @deprecated Use BulkProcessBehavior `fields` settings
 */
	const PUBLICATION_STATE_FIELD = 'status';

/**
 * @deprecated Use BulkProcessBehavior `fields` settings
 */
	const PROMOTION_STATE_FIELD = 'promote';

/**
 * @deprecated
 */
	const UNPROCESSED_ACTION = 'delete';

/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
	public $actsAs = array(
		'Tree',
		'Croogo.BulkProcess' => array(
			'actionsMap' => array(
				'promote' => 'bulkPromote',
				'unpromote' => 'bulkUnpromote',
			),
		),
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
		'q' => array('type' => 'query', 'method' => 'filterPublishedNodes'),
		'filter' => array('type' => 'query', 'method' => 'filterNodes'),
		'title' => array('type' => 'like'),
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
		'published' => true,
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
 * Return filter condition for Nodes
 *
 * @return array Array of conditions
 */
	public function filterPublishedNodes($data = array()) {
		$conditions = array();
		if (!empty($data['filter'])) {
			$filter = '%' . $data['filter'] . '%';
			$conditions = array(
				$this->escapeField('status') => $this->status(),
				'AND' => array(
					array(
						'OR' => array(
							$this->alias . '.title LIKE' => $filter,
							$this->alias . '.excerpt LIKE' => $filter,
							$this->alias . '.body LIKE' => $filter,
							$this->alias . '.terms LIKE' => $filter,
						),
					),
					array(
						$visibilityRolesField => '',
						$visibilityRolesField . ' LIKE' => '%"' . $this->Croogo->roleId() . '"%',

					),
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

		if (empty($data[$this->alias]['path'])) {
			$data[$this->alias]['path'] = $this->_getNodeRelativePath($data);
		}

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

		$idField = $this->escapeField();
		$batch = 30;
		$options = array(
			'order' => $idField,
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
			'limit' => $batch,
		);

		$results = array();
		while ($nodes = $this->find('all', $options)) {
			foreach ($nodes as &$node) {
				$node[$this->alias]['path'] = $this->_getNodeRelativePath($node);
			}
			$result = $this->saveMany($nodes, array('fieldList' => array('path')));
			if ($result === false) {
				$this->log('updateAllNodesPath batch failed:');
				$this->log($this->validationErrors);
				return false;
			}
			$results[] = $result;
			$options['conditions'][$idField . ' >'] = $node[$this->alias]['id'];
			if (count($nodes) < $batch) {
				break;
			}
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
				$this->escapeField('status') => $this->status(),
				$this->escapeField('promote') => self::STATUS_PROMOTED,
				'OR' => array(
					$this->escapeField('visibility_roles') => '',
				),
			);
			$_defaultOrder = $this->escapeField('created') . ' DESC';
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
		$visibilityRolesField = $this->escapeField('visibility_roles');
		$query = Hash::merge(array(
			'conditions' => array(
				$this->escapeField() => $args['id'],
				$this->escapeField('status') => $this->status(),
				'OR' => array(
					$visibilityRolesField => '',
					$visibilityRolesField . ' LIKE' => '%"' . $args['roleId'] . '"%',
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
		$visibilityRolesField = $this->escapeField('visibility_roles');
		$query = Hash::merge(array(
			'conditions' => array(
				$this->escapeField('slug') => $args['slug'],
				$this->escapeField('type') => $args['type'],
				$this->escapeField('status') => $this->status(),
				'OR' => array(
					$visibilityRolesField => '',
					$visibilityRolesField . ' LIKE' => '%"' . $args['roleId'] . '"%',
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
 * Search published nodes
 *
 * $query options:
 *
 * - `q`: term to search
 * - `roleId`: Role Id
 * - `typeAlias`: Type alias
 */
	protected function _findPublished($state, $query, $results = array()) {
		if ($state == 'after') {
			return $results;
		}

		$q = isset($query['q']) ? $query['q'] : null;
		$like = empty($q) ? '%' : '%' . $q . '%';
		$roleId = isset($query['roleId']) ? $query['roleId'] : null;
		$typeAlias = isset($query['typeAlias']) ? $query['typeAlias'] : null;
		$visibilityRolesField = $this->escapeField('visibility_roles');

		$nodeOrConditions = array();

		if ($like) {
			$nodeOrConditions = array_merge($nodeOrConditions, array(
				$this->escapeField('title') . ' LIKE' => $like,
				$this->escapeField('excerpt') . ' LIKE' => $like,
				$this->escapeField('body') . ' LIKE' => $like,
				$this->escapeField('terms') . ' LIKE' => $like,
			));
		}

		$defaults = array(
			'order' => $this->escapeField('created') . ' DESC',
			'limit' => Configure::read('Reading.nodes_per_page'),
			'conditions' => array(
				$this->escapeField('status') => $this->status(),
				'AND' => array(
					array(
						'OR' => $nodeOrConditions,
					),
					array(
						'OR' => array(
							$visibilityRolesField => '',
							$visibilityRolesField . ' LIKE' => '%"' . $roleId . '"%',
						),
					),
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
		);
		if (isset($typeAlias)) {
			$defaults['conditions'][$this->escapeField('type')] = $typeAlias;
		}

		if (empty($query['conditions'])) {
			$query['conditions'] = array();
		}
		$query = Hash::merge($defaults, $query);

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

}
