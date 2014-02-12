<?php

App::uses('ModelBehavior', 'Model/Behavior');

/**
 * TaxonomizableBehavior
 *
 * @category Taxonomy.Model.Behavior
 * @package  Croogo.Taxonomy.Model.Behavior
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TaxonomizableBehavior extends ModelBehavior {

/**
 * Taxonomy instance
 */
	protected $_Taxonomy = null;

/**
 * Setup behavior
 *
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$this->settings[$model->alias] = $config;

		$this->_setupRelationships($model);
		$this->_setupEvents($model);

		$this->_Taxonomy = $model->Taxonomy;
	}

/**
 * Setup relationships
 *
 * @return void
 */
	protected function _setupRelationships(Model $model) {
		$model->bindModel(array(
			'hasAndBelongsToMany' => array(
				'Taxonomy' => array(
					'className' => 'Taxonomy.Taxonomy',
					'with' => 'Taxonomy.ModelTaxonomy',
					'foreignKey' => 'foreign_key',
					'associationForeignKey' => 'taxonomy_id',
					'unique' => true,
					'conditions' => array(
						'model' => $model->alias,
					),
				),
			),
		), false);

		$model->Taxonomy->bindModel(array(
			'hasAndBelongsToMany' => array(
				$model->alias => array(
					'className' => $model->plugin . '.' . $model->alias,
					'with' => 'Taxonomy.ModelTaxonomy',
					'foreignKey' => 'foreign_key',
					'associationForeignKey' => 'taxonomy_id',
					'unique' => true,
					'conditions' => array(
						'model' => $model->alias,
					),
				),
			),
		), false);
	}

/**
 * Setup Event handlers
 *
 * @return void
 */
	protected function _setupEvents($model) {
		$callback = array($this, 'onBeforeSaveNode');
		$eventManager = $model->getEventManager();
		$eventManager->attach($callback, 'Model.Node.beforeSaveNode');
	}

/**
 * Validate Taxonomy data
 */
	public function validateTaxonomyData(Model $model) {
		$typeField = 'type';
		$data =& $model->data;
		if (
			!array_key_exists('Taxonomy', $data) ||
			!array_key_exists('Taxonomy', $data['Taxonomy'])
		) {
			return true;
		}
		$type = $this->_Taxonomy->Vocabulary->Type->find('first', array(
			'fields' => array('id', 'title', 'alias'),
			'contain' => array(
				'Vocabulary' => array(
					'fields' => array('id', 'title', 'alias', 'required', 'multiple'),
				),
			),
			'conditions' => array(
				'alias' => $data[$model->alias][$typeField],
			),
		));
		$selectedTerms = array_filter($data['Taxonomy']['Taxonomy']);

		$result = true;
		$requiredError = __d('croogo', 'Please select at least 1 value');
		$multipleError = __d('croogo', 'Please select at most 1 value');
		foreach ($type['Vocabulary'] as $vocabulary) {
			$fieldName = 'TaxonomyData.' . $vocabulary['id'];
			$terms = $this->_Taxonomy->find('all', array(
				'recursive' => -1,
				'fields' => 'term_id',
				'conditions' => array(
					'vocabulary_id' => $vocabulary['id'],
				),
			));
			$terms = Hash::extract($terms, '{n}.Taxonomy.term_id');
			$selected = count(array_intersect($selectedTerms, $terms));
			if ($vocabulary['required']) {
				if ($selected == 0) {
					$model->invalidate($fieldName, $requiredError);
					$result = false;
				}
			}
			if ($vocabulary['multiple']) {
				if ($selected > 1) {
					$model->invalidate($fieldName, $multipleError);
					$result = false;
				}
			}
		}
		return $result;
	}

/**
 * Transform TaxonomyData array to a format that can be used for save operation
 *
 * @param array $data Array containing relevant Taxonomy data
 * @param string $typeAlias string Node type alias
 * @return array Formatted data
 * @throws InvalidArgumentException
 */
	public function formatTaxonomyData(Model $model, &$data, $typeAlias) {
		$type = $model->Taxonomy->Vocabulary->Type->findByAlias($typeAlias);
		if (empty($type)) {
			throw new InvalidArgumentException(__d('croogo', 'Invalid Content Type'));
		}
		if (empty($data[$model->alias]['type'])) {
			$data[$model->alias]['type'] = $typeAlias;
		}
		$model->type = $type['Type']['alias'];

		if (!$model->Behaviors->enabled('Tree')) {
			$model->Behaviors->attach('Tree', array(
				'scope' => array(
					$model->escapeField('type') => $model->type,
				),
			));
		}

		if (array_key_exists('TaxonomyData', $data)) {
			$data['Taxonomy'] = array('Taxonomy' => array());
			foreach ($data['TaxonomyData'] as $vocabularyId => $taxonomyIds) {
				$data['Taxonomy']['Taxonomy'] = array_merge($data['Taxonomy']['Taxonomy'], (array)$taxonomyIds);
			}
			unset($data['TaxonomyData']);
		}

		$this->cacheTerms($model, $data);
	}

/**
 * Handle Model.Node.beforeSaveNode event
 *
 * @param CakeEvent $event Event containing `data` and `typeAlias`
 */
	public function onBeforeSaveNode($event) {
		$data = $event->data['data'];
		$typeAlias = $event->data['typeAlias'];
		$this->formatTaxonomyData($event->subject, $data, $typeAlias);
		$event->data['data'] = $data;
	}

/**
 * beforeSave
 *
 * @return bool
 */
	public function beforeSave(Model $model, $options = array()) {
		$result = $this->validateTaxonomyData($model);
		if ($result !== true) {
			return $result;
		}
		return true;
	}

/**
 * Caches Term in `terms` field
 *
 * @param Model model
 * @param array $data
 * @return void
 */
	public function cacheTerms(Model $model, &$data = null) {
		if ($data === null) {
			$data =& $model->data;
		}
		if (isset($data['Taxonomy']['Taxonomy'])) {
			$taxonomyIds = $data['Taxonomy']['Taxonomy'];
			$taxonomies = $model->Taxonomy->find('all', array(
				'conditions' => array(
					'Taxonomy.id' => $taxonomyIds,
				),
			));
			$terms = Hash::combine($taxonomies, '{n}.Term.id', '{n}.Term.slug');
			$data[$model->alias]['terms'] = $model->encodeData($terms, array(
				'trim' => false,
				'json' => true,
			));
		}
	}

}
