<?php
App::uses('NodesAppModel', 'Nodes.Model');
App::uses('Router', 'Routing');
/**
 * Node
 *
 * PHP version 5
 *
 * @category Nodes.Model
 * @package  Croogo.Nodes
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
	const STATUS_PUBLISHED = 1;
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
		'Meta.Meta',
		'Croogo.Url',
		'Croogo.Cached' => array(
			'prefix' => array(
				'node_',
				'nodes_',
				'croogo_nodes_',
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

/**
 * Model associations: hasMany
 *
 * @var array
 * @access public
 */
	public $hasMany = array(
		'Comment' => array(
			'className' => 'Comments.Comment',
			'foreignKey' => 'node_id',
			'dependent' => true,
			'conditions' => array('Comment.status' => 1),
			'fields' => '',
			'order' => '',
			'limit' => '5',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => '',
		),
		'Meta' => array(
			'className' => 'Meta.Meta',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array('Meta.model' => 'Node'),
			'fields' => '',
			'order' => 'Meta.key ASC',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => '',
		),
	);

/**
 * Model associations: hasAndBelongsToMany
 *
 * @var array
 * @access public
 */
	public $hasAndBelongsToMany = array(
		'Taxonomy' => array(
			'className' => 'Taxonomy.Taxonomy',
			'with' => 'Taxonomy.NodesTaxonomy',
			'joinTable' => 'nodes_taxonomies',
			'foreignKey' => 'node_id',
			'associationForeignKey' => 'taxonomy_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => '',
		),
	);

	public $findMethods = array(
		'promoted' => true
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
		if ($this->type != null) {
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

		$this->cacheTerms();

		return true;
	}

/**
 * beforeDelete callback
 *
 * @return boolean
 */
	public function beforeDelete($cascade = true) {
		if ($cascade) {
			if (isset($this->hasMany['Comment'])) {
				$this->hasMany['Comment']['conditions'] = '';
			}
		}
		return true;
	}

/**
 * Caches Term in Node.terms field
 *
 * @return void
 */
	public function cacheTerms() {
		if (isset($this->data['Taxonomy']['Taxonomy']) && count($this->data['Taxonomy']['Taxonomy']) > 0) {
			$taxonomyIds = $this->data['Taxonomy']['Taxonomy'];
			$taxonomies = $this->Taxonomy->find('all', array(
				'conditions' => array(
					'Taxonomy.id' => $taxonomyIds,
				),
			));
			$terms = Hash::combine($taxonomies, '{n}.Term.id', '{n}.Term.slug');
			$this->data[$this->alias]['terms'] = $this->encodeData($terms, array(
				'trim' => false,
				'json' => true,
			));
		}
	}

/**
 * Caches Term in Node.terms field
 *
 * @deprecated for backward compatibility
 * @see Node::cacheTerms()
 */
	public function __cacheTerms() {
		return $this->cacheTerms();
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
 */
	public function filterNodes($data = array()) {
		$conditions = array();
		if (!empty($data['filter'])) {
			$filter = '%' . $data['filter'] . '%';
			$conditions = array(
				'OR' => array(
					$this->alias . '.title LIKE'  => $filter,
					$this->alias . '.excerpt LIKE'  => $filter,
					$this->alias . '.body LIKE'  => $filter,
					$this->alias . '.terms LIKE'  => $filter,
				),
			);
		}

		return $conditions;
	}

/**
 * Add a node
 */
	public function add($typeAlias = self::DEFAULT_TYPE, $data = array()) {
		$result = false;

		$data = $this->formatData($data, $typeAlias);
		$result = (bool) $this->saveWithMeta($data);
		Croogo::dispatchEvent('Model.Node.afterAdd', $this, compact('data'));

		return $result;
	}

/**
 * Process action pass as argument
 * @param $action 			string actionToPerfom
 * @param $ids 			nodes ids to perform action upon
 */
	public function processAction($action, $ids){
		$success = true;
		$actionToPerform = strtolower($action);

		if (!in_array($actionToPerform, array_keys($this->actionsMapping))) {
			throw new InvalidArgumentException(__d('nodes', 'Invalid action to perform'));
		}

		if (empty($ids)) {
			throw new InvalidArgumentException(__d('nodes', 'No target to process action upon'));
		}

		if ($actionToPerform === self::UNPROCESSED_ACTION) {
			$success = $this->{$this->actionsMapping[$actionToPerform]}(array($this->escapeField() => $ids));
		} else {
			$success = $this->{$this->actionsMapping[$actionToPerform]}($ids);
		}

		return $success;
	}

/**
 * Prepare data in order to be saved
 * @param $data 			array Node data, and related data such as taxonomy and role
 * @param $typeAlias 		string Node type alias
 * @return $preparedData	array
 */
	public function formatData($data, $typeAlias = self::DEFAULT_TYPE){
		$preparedData = $roles = $type = array();
		$type = $this->Taxonomy->Vocabulary->Type->findByAlias($typeAlias);

		if (!array_key_exists($this->alias, $data)) {
			$preparedData  = array($this->alias => $data);
		} else {
			$preparedData = $data;
		}

		if (empty($type)) {
			throw new InvalidArgumentException(__('Invalid Content Type'));
		}

		$this->type = $type['Type']['alias'];
		if(!$this->Behaviors->enabled('Tree')) {
			$this->Behaviors->attach('Tree', array('scope' => array('Node.type' => $this->type)));
		}
		$this->_parseTaxonomyData($preparedData);
		$preparedData[$this->alias]['path'] = $this->_getNodeRelativePath($preparedData);

		if (!array_key_exists('Role', $preparedData) || empty($preparedData['Role']['Role'])) {
			$roles = '';
		} else {
			$roles = $preparedData['Role']['Role'];
		}

		$preparedData[$this->alias]['visibility_roles'] = $this->encodeData($roles);
		unset($this->type);

		return $preparedData;
	}

	public function updateAllNodesPaths(){
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

		return $this->saveMany($nodes);
	}

	protected function _parseTaxonomyData(&$nodeData) {
		if (array_key_exists('TaxonomyData', $nodeData)) {
			$nodeData['Taxonomy'] = array('Taxonomy' => array());
			foreach ($nodeData['TaxonomyData'] as $vocabularyId => $taxonomyIds) {
				$nodeData['Taxonomy']['Taxonomy'] = array_merge($nodeData['Taxonomy']['Taxonomy'], (array) $taxonomyIds);
			}
			unset($nodeData['TaxonomyData']);
		}
	}

	protected function _getNodeRelativePath($data){
		return Croogo::getRelativePath(array(
			'plugin' => 'nodes',
			'admin' => false,
			'controller' => 'nodes',
			'action' => 'view',
			'type' => $this->_getType($data),
			'slug' => $data[$this->alias]['slug'],

		));
	}

	protected function _getType($data){
		if (empty($data[$this->alias]['type'])) {
			$type = is_null($this->type) ? self::DEFAULT_TYPE : $this->type;
		} else {
			$type = $data[$this->alias]['type'];
		}

		return $type;
	}

	protected function _findPromoted($state, $query, $results = array()){
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
				'Node.status' => self::STATUS_PUBLISHED,
				'Node.promote' => self::STATUS_PROMOTED,
				'OR' => array(
					'Node.visibility_roles' => '',
				),
			);
			$_defaultOrder = $this->alias . '.created DESC';
			$_defaultLimit = Configure::read('Reading.nodes_per_page');

			foreach ($_defaultFilters as $filter){
				$this->__mergeQueryFilters($query, $filter, ${'_default' . ucfirst($filter)});
			}

			return $query;
		} else {
			return $results;
		}
	}

	protected function _publish($ids){
		return $this->__saveStatuts($ids, self::PUBLICATION_STATE_FIELD, self::STATUS_PUBLISHED);
	}

	protected function _unpublish($ids){
		return $this->__saveStatuts($ids, self::PUBLICATION_STATE_FIELD, self::STATUS_UNPUBLISHED);
	}
	protected function _promote($ids){
		return $this->__saveStatuts($ids, self::PROMOTION_STATE_FIELD, self::STATUS_PROMOTED);
	}

	protected function _unpromote($ids){
		return $this->__saveStatuts($ids, self::PROMOTION_STATE_FIELD, self::STATUS_UNPROMOTED);
	}

	private function __saveStatuts($ids, $field, $status){
		return $this->updateAll(array($this->escapeField($field) => $status), array($this->escapeField() => $ids));

	}

	private function __mergeQueryFilters(&$query, $key, $values){
		if (!empty($query[$key])) {
			$query[$key] = Hash::merge($query[$key], $values);
		} else {
			$query[$key] = $values;
		}
	}
}
