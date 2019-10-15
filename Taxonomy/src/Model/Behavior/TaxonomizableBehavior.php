<?php

namespace Croogo\Taxonomy\Model\Behavior;

use ArrayObject;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\Log\Log;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use Croogo\Taxonomy\Model\Entity\Term;

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
class TaxonomizableBehavior extends Behavior
{
    /**
     * @param array $config
     * @return void
     */
    public function initialize(array $config)
    {
        $this->_setupRelationships();

        $this->_table->searchManager()
            ->add('vocab', 'Search.Finder', [
                'finder' => 'withVocabulary',
            ])
            ->add('term', 'Search.Finder', [
                'finder' => 'withTerm',
            ]);
    }

    /**
     * Setup Event handlers
     */
    public function implementedEvents()
    {
        $events = parent::implementedEvents();
//        $events['Model.Node.beforeSaveNode'] = 'beforeSaveNode';

        return $events;
    }

    /**
     * Setup relationships
     *
     * @return void
     */
    protected function _setupRelationships()
    {
        $this->_table->belongsTo('Types', [
            'className' => 'Croogo/Taxonomy.Types',
            'foreignKey' => 'type',
            'bindingKey' => 'alias',
            'propertyName' => 'node_type',
        ]);
        $this->_table->belongsToMany('Taxonomies', [
            'className' => 'Croogo/Taxonomy.Taxonomies',
            'through' => 'Croogo/Taxonomy.ModelTaxonomies',
            'foreignKey' => 'foreign_key',
            'associationForeignKey' => 'taxonomy_id',
            'conditions' => [
                'model' => $this->_table->getRegistryAlias(),
            ],
        ]);
        $this->_table->Taxonomies->belongsToMany($this->_table->getAlias(), [
            'targetTable' => $this->_table,
            'through' => 'Croogo/Taxonomy.ModelTaxonomies',
            'foreignKey' => 'foreign_key',
            'associationForeignKey' => 'taxonomy_id',
            'conditions' => [
                'model' => $this->_table->getRegistryAlias(),
            ],
        ]);
    }

    /**
     * Get selected terms from data
     */
    protected function _getSelectedTerms(Entity $entity)
    {
        if ($entity->has('taxonomies')) {
            return Hash::extract($entity->taxonomies, '{n}.id');
        } else {
            return [];
        }
    }

    /**
     * Validate Taxonomy data
     */
    public function validateTaxonomyData(Entity $entity)
    {
        $typeField = 'type';

        if ($entity->has($typeField)) {
            $typeAlias = $entity->{$typeField};
        } else {
            Log::error('Unable to determine type for model ' . $this->_table->getAlias());

            return false;
        }

        $type = $this->_table->Taxonomies->Vocabularies->Types->find()
            ->select([
                'id',
                'title',
                'alias',
            ])
            ->contain([
                'Vocabularies' => function (Query $q) {
                    return $q
                        ->select([
                            'id',
                            'title',
                            'alias',
                            'required',
                            'multiple',
                        ])
                        ->contain(['Taxonomies']);
                },
            ])
            ->where([
                'alias' => $typeAlias,
            ])
            ->first();

        if (empty($type)) {
            Log::error('Type ' . $typeAlias . ' cannot be found');

            return true;
        }

        $selectedTerms = $this->_getSelectedTerms($entity);

        $requiredError = __d('croogo', 'Please select at least 1 value');
        $multipleError = __d('croogo', 'Please select at most 1 value');
        foreach ($type->vocabularies as $vocabulary) {
            $fieldName = 'taxonomy_data.' . $vocabulary->id;
            $terms = Hash::extract($vocabulary, 'taxonomies.{n}.id');
            $selected = count(array_intersect($selectedTerms, $terms));
            if ($vocabulary->required && $selected == 0) {
                $entity->errors($fieldName, $requiredError);
            }
            if (!$vocabulary->multiple && $selected > 1) {
                $entity->errors($fieldName, $multipleError);
            }
        }
    }

    /**
     * Transform TaxonomyData array to a format that can be used for save operation
     *
     * @param \Cake\ORM\Entity $entity Entity being saved
     * @param string $typeAlias string Node type alias
     * @return void
     * @throws RecordNotFoundException
     */
    public function formatTaxonomyData(Entity $entity, $typeAlias)
    {
        $type = $this->_table->Taxonomies->Vocabularies->Types->findByAlias($typeAlias)
            ->first();
        if (empty($type)) {
            throw new RecordNotFoundException(__d('croogo', 'Invalid Content Type'));
        }
        $entity->type = $type->alias;
        if (!$this->_table->behaviors()->has('Tree')) {
            $this->_table->addBehavior('Tree', [
                'scope' => [
                    $this->_table->aliasField('type') => $entity->type,
                ],
            ]);
        }
        if ($entity->has('taxonomy_data')) {
            $taxonomies = [];
            foreach ($entity->taxonomy_data as $vocabularyId => $taxonomyIds) {
                if (empty($taxonomyIds)) {
                    continue;
                }
                foreach ((array)$taxonomyIds as $taxonomyId) {
                    if (!is_numeric($taxonomyId)) {
                        $term = $this->findOrCreateTerm($entity, $vocabularyId, $taxonomyId);
                        $taxonomy = $this->findTermTaxonomy($term, $vocabularyId);
                        $taxonomyId = $taxonomy->id;
                    }
                    $taxonomies[] = [
                        'id' => $taxonomyId,
                        '_joinData' => [
                            'model' => $entity->getSource(),
                        ],
                    ];
                }
            }
            $this->_table->patchEntity($entity, compact('taxonomies'));
        }
    }

    /**
     * Find or create term and linkage with the relevant taxonomy and vocabulary
     *
     * @param Cake\ORM\Entity $entity Entity
     * @param int $vocabularyId Vocabulary Id
     * @param string $taxonomyId Taxonomy ID
     * @return \Croogo\Taxonomy\Model\Entity\Term
     */
    private function findOrCreateTerm(Entity $entity, int $vocabularyId, $taxonomyId)
    {
        $Terms = $this->_table->Taxonomies->Terms;
        $term = $Terms->find()
            ->where([
                'title' => $taxonomyId,
            ])->first();

        if (!$term) {
            $term = $Terms->newEntity([
                'title' => $taxonomyId,
                'slug' => Text::slug(strtolower($taxonomyId)),
                'vocabularies' => [
                    ['id' => $vocabularyId],
                ],
                'taxonomies' => [
                    ['model' => $entity->getSource()],
                ],
            ], [
                'associated' => [
                    'vocabularies',
                ]
            ]);
            $Terms->setScopeForTaxonomy($vocabularyId);
            $term = $Terms->save($term, [
                'associated' => [
                    'vocabularies',
                ],
            ]);
        }

        return $term;
    }

    private function findTermTaxonomy(Term $term, int $vocabularyId)
    {
        $Taxonomies = $this->_table->Taxonomies;

        return $Taxonomies->find()
            ->where([
                'vocabulary_id' => $vocabularyId,
                'term_id' => $term->id,
            ])
            ->first();
    }

    /**
     * beforeSave
     *
     * @return void
     */
    public function beforeSave(Event $event, Entity $entity, ArrayObject $options)
    {
        if (!$entity->has('taxonomy_data')) {
            return;
        }

        if (isset($options['associated']) &&
            !(isset($options['associated']['Taxonomies']) || in_array('Taxonomies', $options['associated']))
        ) {
            $options['associated'][] = 'Taxonomies';
        }
        $this->formatTaxonomyData($entity, $entity->type);
        $this->validateTaxonomyData($entity);
    }

    /**
     * @param Event $event
     * @param Query $query
     *
     * @return array|Query
     */
    public function beforeFind(Event $event, Query $query)
    {
        return $query->contain([
            'Taxonomies' => [
                'Terms',
                'Vocabularies',
            ],
            'Types',
        ]);
    }

    /**
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findWithTerm(Query $query, array $options)
    {
        if (empty($options['term'])) {
            return $query;
        }
        $term = $options['term'];

        if (is_string($term)) {
            $locale = I18n::getLocale();
            $cacheKeys = ['term', $locale, $term];
            $cacheKey = implode('_', $cacheKeys);
            $entity = $this->_table->Taxonomies->Terms->find()
                ->where([
                    'Terms.slug' => $term
                ])
                ->cache($cacheKey, 'nodes_term')
                ->first();
        }

        if (!$entity) {
            throw new RecordNotFoundException(__d('croogo', 'Term not found: %s', $term));
        }

        $query
            ->matching('Taxonomies', function (Query $q) use ($entity) {
                return $q
                   ->where([
                       'term_id' => $entity->id
                   ]);
            });

        return $query;
    }

    /**
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findWithVocabulary(Query $query, array $options)
    {
        if (empty($options['vocab'])) {
            return $query;
        }
        $vocab = $options['vocab'];

        if (is_string($vocab)) {
            $locale = I18n::getLocale();
            $cacheKeys = ['term', $locale, $vocab];
            $cacheKey = implode('_', $cacheKeys);
            $entity = $this->_table->Taxonomies->Vocabularies->find()
                ->where([
                    'Vocabularies.alias' => $vocab
                ])
                ->cache($cacheKey, 'croogo_vocabularies')
                ->first();
        }

        if (!$entity) {
            throw new RecordNotFoundException(__d('croogo', 'Vocabulary not found: {0}', $vocab));
        }

        $query
            ->matching('Taxonomies', function (Query $q) use ($entity) {
                return $q
                   ->where([
                       'vocabulary_id' => $entity->id
                   ]);
            });

        return $query;
    }
}
