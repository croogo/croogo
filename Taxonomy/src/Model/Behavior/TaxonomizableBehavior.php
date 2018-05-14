<?php

namespace Croogo\Taxonomy\Model\Behavior;

use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\Log\Log;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\Utility\Hash;

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
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->_setupRelationships();
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
        $this->_table->belongsToMany(
            'Taxonomies',
            [
                'className' => 'Croogo/Taxonomy.Taxonomies',
                'through' => 'Croogo/Taxonomy.ModelTaxonomies',
                'foreignKey' => 'foreign_key',
                'associationForeignKey' => 'taxonomy_id',
                'conditions' => [
                    'model' => $this->_table->registryAlias(),
                ],
            ]
        );
        $this->_table->Taxonomies->belongsToMany(
            $this->_table->alias(),
            [
                'targetTable' => $this->_table,
                'through' => 'Croogo/Taxonomy.ModelTaxonomies',
                'foreignKey' => 'foreign_key',
                'associationForeignKey' => 'taxonomy_id',
                'conditions' => [
                    'model' => $this->_table->registryAlias(),
                ],
            ]
        );
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
            Log::error('Unable to determine type for model ' . $this->_table->alias());

            return false;
        }

        $type = $this->_table->Taxonomies->Vocabularies->Types->find()
            ->select(
                [
                    'id',
                    'title',
                    'alias',
                ]
            )
            ->contain(
                [
                    'Vocabularies' => function (Query $q) {
                        return $q->select(
                            [
                                'id',
                                'title',
                                'alias',
                                'required',
                                'multiple',
                            ]
                        )->contain(['Taxonomies']);
                    },
                ]
            )
            ->where([
                'alias' => $typeAlias
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
     * @throws InvalidArgumentException
     */
    public function formatTaxonomyData(Entity $entity, $typeAlias)
    {
        $type = $this->_table->Taxonomies->Vocabularies->Types->findByAlias($typeAlias)
            ->first();
        if (empty($type)) {
            throw new InvalidArgumentException(__d('croogo', 'Invalid Content Type'));
        }
        $entity->type = $type->alias;
        if (!$this->_table->behaviors()
            ->has('Tree')
        ) {
            $this->_table->addBehavior(
                'Tree',
                [
                    'scope' => [
                        $this->_table->aliasField('type') => $entity->type,
                    ],
                ]
            );
        }
        if ($entity->has('taxonomy_data')) {
            $taxonomies = [];
            foreach ($entity->taxonomy_data as $vocabularyId => $taxonomyIds) {
                if (empty($taxonomyIds)) {
                    continue;
                }
                foreach ((array)$taxonomyIds as $taxonomyId) {
                    $taxonomies[] = [
                        'id' => $taxonomyId,
                        '_joinData' => [
                            'model' => $entity->source(),
                        ],
                    ];
                }
            }
            $this->_table->patchEntity($entity, compact('taxonomies'));
        }
    }

    /**
     * beforeSave
     *
     * @return bool
     */
    public function beforeSave(Event $event, Entity $entity)
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

    public function beforeFind(Event $event, Query $query)
    {
        $query->contain(['Taxonomies']);
    }

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
            $term = $this->_table->Taxonomies->Terms->find()
                ->where([
                    'Terms.slug' => $term
                ])
                ->cache($cacheKey, 'nodes_term')
                ->first();
        }

        if (!$term) {
            return $query;
        }

        $query
            ->matching('Taxonomies', function (Query $q) use ($term) {
               return $q
                   ->where([
                       'term_id' => $term->id
                   ]);
            });

        return $query;
    }
}
