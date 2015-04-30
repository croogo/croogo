<?php

namespace Croogo\Taxonomy\Model\Table;

use Cake\ORM\Query;
use Croogo\Croogo\Model\Table\CroogoTable;

/**
 * Term
 *
 * @property VocabulariesTable Vocabularies
 * @category Taxonomy.Model
 * @package  Croogo.Taxonomy.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TermsTable extends CroogoTable {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	public $name = 'Term';

/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
	public $actsAs = array(
		'Croogo.Cached' => array(
			'groups' => array(
				'taxonomy',
				'nodes',
			),
		),
		'Croogo.Trackable',
	);

/**
 * Validation
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'slug' => array(
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'This slug has already been taken.',
			),
			'minLength' => array(
				'rule' => array('minLength', 1),
				'message' => 'Slug cannot be empty.',
			),
		),
	);

/**
 * Model associations: hasAndBelongsToMany
 *
 * @var array
 * @access public
 */
	public $hasAndBelongsToMany = array(
		'Vocabulary' => array(
			'className' => 'Taxonomy.Vocabulary',
			'with' => 'Taxonomy',
			'joinTable' => 'taxonomy',
			'foreignKey' => 'term_id',
			'associationForeignKey' => 'vocabulary_id',
			'unique' => true,
		),
	);

	public function initialize(array $config) {
		parent::initialize($config);

		$this->belongsToMany('Croogo/Taxonomy.Vocabularies', [
			'joinTable' => 'Croogo/Taxonomy.Taxonomies',
			'foreignKey' => 'term_id',
			'targetForeignKey' => 'vocabulary_id',
		]);
	}

/**
 * Save Term and return ID.
 * If another Term with same slug exists, return ID of that Term without saving.
 *
 * @param  array $data
 * @return integer
 */
	public function saveAndGetId($data) {
		$dataToPersist = $data;
		if (!array_key_exists($this->alias, $data)) {
			$dataToPersist = array($this->alias => $data);
		}
		$term = $this->find('first', array(
			'conditions' => array(
				$this->escapeField('slug') => $dataToPersist[$this->alias]['slug'],
			),
		));
		if (isset($term[$this->alias][$this->primaryKey])) {
			$id = $term[$this->alias][$this->primaryKey];
			$update = $dataToPersist[$this->alias];
			if ($id && isset($update['description'])) {
				$this->id = $id;
				$this->saveField('description', $update['description']);
			}
			return $id;
		}

		$this->id = false;
		if ($this->save($dataToPersist)) {
			return $this->id;
		}
		return false;
	}

/**
 * Allow delete on whether given Term has any association left with Taxonomy
 *
 * @return bool
 */
	public function beforeDelete($cascade = true) {
		$Taxonomy = ClassRegistry::init('Taxonomy.Taxonomy');
		$count = $Taxonomy->find('count', array(
			'recursive' => -1,
			'conditions' => array(
				$Taxonomy->escapeField('term_id') => $this->id,
			),
		));
		return $count === 0;
	}

/**
 * Convenience method to check whether term exists within a vocabulary
 *
 * @param integer $id Term Id
 * @param integer $vocabularyId Vocabulary Id
 * @param integer $taxonomyId Taxonomy Id
 * @return bool True if Term exists in Vocabulary
 */
	public function isInVocabulary($id, $vocabularyId, $taxonomyId = null) {
		$conditions = array('term_id' => $id, 'vocabulary_id' => $vocabularyId);
		if (!is_null($taxonomyId)) {
			$conditions['Taxonomy.id !='] = $taxonomyId;
		}
		return $this->Vocabulary->Taxonomy->hasAny($conditions);
	}

/**
 * Save term
 *
 * @see Term::_save()
 * @return array|bool Array of saved term or boolean false
 */
	public function add($data, $vocabularyId) {
		return $this->_save($data, $vocabularyId);
	}

/**
 * Edit term
 *
 * @see Term::_save()
 * @return array|bool Array of saved term or boolean false
 */
	public function edit($data, $vocabularyId) {
		$id = $data[$this->alias][$this->primaryKey];
		$slug = $data[$this->alias]['slug'];

		if ($this->hasSlugChanged($id, $slug) && $this->slugExists($slug)) {
			$edited = false;
		} else {
			$taxonomyId = !empty($data['Taxonomy']['id']) ? $data['Taxonomy']['id'] : null;
			$edited = $this->_save($data, $vocabularyId, $taxonomyId);
		}
		return $edited;
	}

/**
 * Checks wether slug has changed for given Term id
 *
 * @param int $id Term Id
 * @param string $slug Slug
 * @return bool True if slug has changed
 * @throws NotFoundException
 */
	public function hasSlugChanged($id, $slug) {
		if (!is_numeric($id) || !$this->exists($id)) {
			throw new NotFoundException(__d('croogo', 'Invalid Term Id'));
		}
		return $this->field('slug', array($this->escapeField() => $id)) != $slug;
	}

/**
 * Convenience check for slug
 *
 * @return bool
 */
	public function slugExists($slug) {
		return $this->hasAny(compact('slug'));
	}

/**
 * Remove term
 *
 * @param integer $id Term Id
 * @param integer $vocabularyId Vocabulary Id
 */
	public function remove($id, $vocabularyId) {
		$taxonomyId = $this->Vocabulary->Taxonomy->field('id', array(
			'term_id' => $id, 'vocabulary_id' => $vocabularyId
		));
		$this->setScopeForTaxonomy($vocabularyId);
		return $this->Taxonomy->delete($taxonomyId) && $this->delete($id);
	}

	public function findByVocabulary(Query $query, array $options) {
		if (empty($options['vocabulary_id'])) {
			trigger_error(__d('croogo', '"vocabulary_id" key not found'));
		}

		$vocabulary = $this->Vocabularies->find()->select('alias')->where(['id' => $options['vocabulary_id']])->first();

		$termsId = $this->Vocabularies->Taxonomies->getTree($vocabulary->alias, [
			'key' => 'id', 'value' => 'title'
		]);

		$query->where([
			$this->primaryKey() .' IN' => array_keys($termsId)
		]);

		return $query;
	}

/**
 * Save new/updated term data
 *
 * @param array $data Term data
 * @param integer $vocabularyId Vocabulary Id
 * @param integer $taxonomyId Taxonomy Id
 */
	protected function _save($data, $vocabularyId, $taxonomyId = null) {
		$added = false;

		$termId = $this->saveAndGetId($data);
		if (!$this->isInVocabulary($termId, $vocabularyId, $taxonomyId)) {
			$this->setScopeForTaxonomy($vocabularyId);
			$dataToPersist = array(
				'parent_id' => $data['Taxonomy']['parent_id'],
				'term_id' => $termId,
				'vocabulary_id' => $vocabularyId,
			);
			if (!is_null($taxonomyId)) {
				$dataToPersist['id'] = $taxonomyId;
			}
			$added = $this->Taxonomy->save($dataToPersist);
		}
		return $added;
	}

/**
 * Set Scope
 *
 * @param integer $vocabularyId Vocabulary Id
 */
	public function setScopeForTaxonomy($vocabularyId) {
		$scopeSettings = array('scope' => array(
			'Taxonomy.vocabulary_id' => $vocabularyId,
		));
		if ($this->Vocabulary->Taxonomy->Behaviors->loaded('Tree')) {
			$this->Vocabulary->Taxonomy->Behaviors->Tree->setup($this->Vocabulary->Taxonomy, $scopeSettings);
		} else {
			$this->Vocabulary->Taxonomy->Behaviors->load('Tree', $scopeSettings);
		}
	}
}
