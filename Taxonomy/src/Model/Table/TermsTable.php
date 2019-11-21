<?php

namespace Croogo\Taxonomy\Model\Table;

use ArrayObject;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;
use Croogo\Core\Model\Table\CroogoTable;
use Croogo\Taxonomy\Model\Entity\Term;
use Exception;

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
class TermsTable extends CroogoTable
{

    public function initialize(array $config)
    {
        $this->addBehavior('Search.Search');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Croogo/Core.Trackable');

        $this->belongsToMany('Croogo/Taxonomy.Vocabularies', [
            'through' => 'Croogo/Taxonomy.Taxonomies',
            'foreignKey' => 'term_id',
            'targetForeignKey' => 'vocabulary_id',
        ]);
        $this->hasMany('Croogo/Taxonomy.Taxonomies');

        $this->searchManager()
            ->add('vocab', 'Search.Callback', [
                'callback' => function ($query, $args, $filter) {
                    return $query->matching('Vocabularies', function ($query) use ($args) {
                        return $query->where([
                            'Vocabularies.alias' => $args['vocab'],
                        ]);
                    });
                },
            ]);
    }

    protected function _initializeSchema(TableSchema $table)
    {
        $table->setColumnType('params', 'params');

        return parent::_initializeSchema($table);
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->notBlank('title', __d('croogo', 'The title cannot be empty'))
            ->notBlank('slug', __d('croogo', 'The slug cannot be empty'))
            ->add('slug', [
                'unique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __d('croogo', 'This slug has already been taken.')
                ]
            ]);

        return $validator;
    }

    /**
     * @param \Cake\ORM\RulesChecker $rules
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules
            ->add($rules->isUnique(
                ['alias'],
                __d('croogo', 'That alias is already taken')
            ));

        return $rules;
    }

    /**
     * Allow delete on whether given Term has any association left with Taxonomy
     *
     * @return bool
     */
    public function beforeDelete(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $count = $this->Taxonomies->find()
            ->where([
                $this->Taxonomies->aliasField('term_id') => $entity->id,
            ])
            ->count();
        if ($count > 0) {
            throw new Exception('Term is still in use.');
        }

        return $count === 0;
    }

    /**
     * Save term
     *
     * @see Term::_save()
     * @return array|bool Array of saved term or boolean false
     */
    public function add($data, $vocabularyId)
    {
        return $this->_save($data, $vocabularyId);
    }

    /**
     * Edit term
     *
     * @see Term::_save()
     * @return array|bool Array of saved term or boolean false
     */
    public function edit(Entity $entity, $vocabularyId)
    {
        if ($entity->isDirty('slug') && $this->slugExists($entity->slug)) {
            $edited = false;
        } else {
            $edited = $this->_save($entity, $vocabularyId);
        }

        return $edited;
    }

    /**
     * Convenience check for slug
     *
     * @return bool
     */
    public function slugExists($slug)
    {
        return $this->exists(compact('slug'));
    }

    /**
     * Remove term
     *
     * @param int $id Term Id
     * @param int $vocabularyId Vocabulary Id
     */
    public function remove($id, $vocabularyId)
    {
        $taxonomy = $this->Vocabularies->Taxonomies->find()
            ->select(['id'])
            ->where([
                'term_id' => $id,
                'vocabulary_id' => $vocabularyId,
            ])
            ->first();
        $this->setScopeForTaxonomy($vocabularyId);
        $term = $this->get($id);
        $deleted = $this->Taxonomies->delete($taxonomy);

        $termUsageCount = $this->Vocabularies->Taxonomies->find()
            ->select(['id'])
            ->where([
                'term_id' => $id,
            ])
            ->count();
        if ($termUsageCount === 0) {
            $deleted &= $this->delete($term);
        }

        return $deleted;
    }

    public function findByVocabulary(Query $query, array $options)
    {
        if (empty($options['vocabulary_id'])) {
            trigger_error(__d('croogo', '"vocabulary_id" key not found'));
        }

        $vocabulary = $this->Vocabularies->find()->select('alias')->where(['id' => $options['vocabulary_id']])->first();

        $termsId = $this->Vocabularies->Taxonomies->getTree($vocabulary->alias, [
            'key' => 'id', 'value' => 'title'
        ]);

        if (empty($termsId)) {
            $query->where([
                '1 = 0'
            ]);
        } else {
            $query->where([
                $this->aliasField($this->primaryKey()) . ' IN' => array_keys($termsId)
            ]);
        }

        return $query;
    }

    /**
     * Save new/updated term data
     *
     * @param Entity $entity Term
     * @param int $vocabularyId Vocabulary Id
     */
    protected function _save(Term $entity, $vocabularyId)
    {
        $this->setScopeForTaxonomy($vocabularyId);
        $term = $this->save($entity, [
            'associated' => ['Taxonomies'],
        ]);

        return $term;
    }

    /**
     * Set Scope
     *
     * @param int $vocabularyId Vocabulary Id
     */
    public function setScopeForTaxonomy($vocabularyId)
    {
        $scopeSettings = ['scope' => [
            'Taxonomies.vocabulary_id' => $vocabularyId,
        ]];
        if ($this->Vocabularies->Taxonomies->hasBehavior('Tree')) {
            $this->Vocabularies->Taxonomies->behaviors()->get('Tree')->setConfig($scopeSettings);
        } else {
            $this->Vocabularies->Taxonomies->addBehavior('Tree', $scopeSettings);
        }
    }
}
