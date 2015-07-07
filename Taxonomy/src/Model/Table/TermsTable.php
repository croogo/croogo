<?php

namespace Croogo\Taxonomy\Model\Table;

use Cake\ORM\Entity;
use Cake\ORM\Query;
use Croogo\Core\Model\Table\CroogoTable;

/**
 * Term
 *
 * @property VocabulariesTable Vocabularies
 * @property TaxonomiesTable Taxonomies
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
			'through' => 'Croogo/Taxonomy.Taxonomies',
			'foreignKey' => 'term_id',
			'targetForeignKey' => 'vocabulary_id',
		]);
		$this->hasMany('Croogo/Taxonomy.Taxonomies');
	}

/**
 * Save Term and return ID.
 * If another Term with same slug exists, return ID of that Term without saving.
 *
 * @param  Entity $entity
 * @return integer
 */
	public function saveAndGetId(Entity $entity) {
		$term = $this->find()->where([
			'slug' => $entity->slug,
		])->first();
		if ($term) {
			$id = $term->{$this->primaryKey()};
			if ($id && $entity->dirty('description')) {
				$this->id = $id;
				$this->save($entity);
			}
			return $id;
		}

		$savedEntity = $this->save($entity);
		if ($savedEntity) {
			return $savedEntity->id;
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
			$conditions['Taxonomies.id !='] = $taxonomyId;
		}
		return (bool) $this->Vocabularies->Taxonomies->find('all')->where($conditions)->count();
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
	public function edit(Entity $entity, $vocabularyId) {
		if ($entity->dirty('slug') && $this->slugExists($entity->slug)) {
			$edited = false;
		} else {
			$taxonomyId = $entity->taxonomies[0]->id;
			$edited = $this->_save($entity, $vocabularyId, $taxonomyId);
		}
		return $edited;
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
 * @param Entity $entity Term \\
 * @param integer $vocabularyId Vocabulary Id
 * @param integer $taxonomyId Taxonomy Id
 */
	protected function _save(Entity $entity, $vocabularyId, $taxonomyId = null) {
		$added = false;

		$termId = $this->saveAndGetId($entity);
		if (!$this->isInVocabulary($termId, $vocabularyId, $taxonomyId)) {
			$this->setScopeForTaxonomy($vocabularyId);
			$dataToPersist = (!is_null($taxonomyId)) ? $this->Taxonomies->get($taxonomyId) : $this->Taxonomies->newEntity();

			$dataToPersist = $this->Taxonomies->patchEntity($dataToPersist, [
				'parent_id' => $entity->parent_id,
				'term_id' => $termId,
				'vocabulary_id' => $vocabularyId,
			]);

			$added = $this->Taxonomies->save($dataToPersist);
		}
		return $added;
	}

/**
 * Set Scope
 *
 * @param integer $vocabularyId Vocabulary Id
 */
	public function setScopeForTaxonomy($vocabularyId) {
		$scopeSettings = ['scope' => [
			'Taxonomies.vocabulary_id' => $vocabularyId,
		]];
		if ($this->Vocabularies->Taxonomies->hasBehavior('Tree')) {
			$this->Vocabularies->Taxonomies->behaviors()->get('Tree')->config($scopeSettings);
		} else {
			$this->Vocabularies->Taxonomies->addBehavior('Tree', $scopeSettings);
		}
	}
}
