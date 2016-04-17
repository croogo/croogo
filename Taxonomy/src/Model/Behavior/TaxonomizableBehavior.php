<?php

namespace Croogo\Taxonomy\Model\Behavior;

use Cake\Event\Event;
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
                'through' => 'Croogo/Taxonomy.ModelTaxonomy',
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
    protected function _getSelectedTerms($data)
    {
        if (isset($data['Taxonomy']['Taxonomy'])) {
            return array_filter($data['Taxonomy']['Taxonomy']);
        } elseif (isset($data['Taxonomy'])) {
            return Hash::extract($data['Taxonomy'], '{n}.taxonomy_id');
        } else {
            return [];
        }
    }

    /**
     * Validate Taxonomy data
     */
    public function validateTaxonomyData(Model $model)
    {
        $typeField = 'type';
        $data =& $model->data;

        if (isset($data[$model->alias][$typeField])) {
            $typeAlias = $data[$model->alias][$typeField];
        } elseif (isset($model->type)) {
            $typeAlias = $model->type;
        } else {
            Log::error('Unable to determine type for model ' . $model->alias);

            return false;
        }

        $type = $this->Taxonomies->Vocabulary->Type->find(
            'first',
            [
                'fields' => ['id', 'title', 'alias'],
                'contain' => [
                    'Vocabulary' => [
                        'fields' => ['id', 'title', 'alias', 'required', 'multiple'],
                    ],
                ],
                'conditions' => [
                    'alias' => $typeAlias,
                ],
            ]
        );

        if (empty($type)) {
            Log::error('Type ' . $typeAlias . ' cannot be found');

            return true;
        }

        $selectedTerms = $this->_getSelectedTerms($data);

        $result = true;
        $requiredError = __d('croogo', 'Please select at least 1 value');
        $multipleError = __d('croogo', 'Please select at most 1 value');
        foreach ($type['Vocabulary'] as $vocabulary) {
            $fieldName = 'TaxonomyData.' . $vocabulary['id'];
            $terms = $this->Taxonomies->find(
                'all',
                [
                    'recursive' => -1,
                    'fields' => 'term_id',
                    'conditions' => [
                        'vocabulary_id' => $vocabulary['id'],
                    ],
                ]
            );
            $terms = Hash::extract($terms, '{n}.Taxonomy.term_id');
            $selected = count(array_intersect($selectedTerms, $terms));
            if ($vocabulary['required']) {
                if ($selected == 0) {
                    $model->invalidate($fieldName, $requiredError);
                    $result = false;
                }
            }
            if (!$vocabulary['multiple']) {
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
    public function formatTaxonomyData(Model $model, &$data, $typeAlias)
    {
        $type = $model->Taxonomy->Vocabulary->Type->findByAlias($typeAlias);
        if (empty($type)) {
            throw new InvalidArgumentException(__d('croogo', 'Invalid Content Type'));
        }
        if (empty($data[$model->alias]['type'])) {
            $data[$model->alias]['type'] = $typeAlias;
        }
        $model->type = $type['Type']['alias'];

        if (!$model->Behaviors->enabled('Tree')) {
            $model->Behaviors->attach(
                'Tree',
                [
                    'scope' => [
                        $model->escapeField('type') => $model->type,
                    ],
                ]
            );
        }

        if (array_key_exists('TaxonomyData', $data)) {
            $foreignKey = $model->id;
            if (isset($data[$model->alias][$model->primaryKey])) {
                $foreignKey = $data[$model->alias][$model->primaryKey];
            }
            $data['Taxonomy'] = [];
            foreach ($data['TaxonomyData'] as $vocabularyId => $taxonomyIds) {
                if (empty($taxonomyIds)) {
                    continue;
                }
                foreach ((array)$taxonomyIds as $taxonomyId) {
                    $join = [
                        'model' => $model->alias,
                        'foreign_key' => $foreignKey,
                        'taxonomy_id' => $taxonomyId,
                    ];
                    $data['Taxonomy'][] = $join;
                }
            }
            unset($data['TaxonomyData']);
        }

        $this->cacheTerms($model, $data);
    }

    /**
     * Handle Model.Node.beforeSaveNode event
     *
     * @param Event $event Event containing `data` and `typeAlias`
     */
    public function beforeMarshal($event)
    {
//        $data = $event->data['data'];
//        $typeAlias = $event->data['typeAlias'];
//        $this->formatTaxonomyData($event->subject, $data, $typeAlias);
//        $event->data['data'] = $data;
    }

    /**
     * Handle Model.Node.afterSaveNode event
     *
     * @param CakeEvent $event Event containing `data` and `typeAlias`
     */
    public function onAfterSaveNode($event)
    {
//        $model = $event->subject;
//        $data = $event->data['data'];
//        if (isset($model->id)) {
//            $id = $model->id;
//        }
//        if (isset($data[$model->alias][$model->primaryKey])) {
//            $id = $data[$model->alias][$model->primaryKey];
//        }
//        if ($id && array_key_exists('Taxonomy', $data) && empty($data['Taxonomy'])) {
//            $model->Taxonomy->ModelTaxonomy->deleteAll(
//                [
//                    'model' => $model->alias,
//                    'foreign_key' => $id,
//                ]
//            );
//        }
    }

    /**
     * beforeSave
     *
     * @return bool
     */
    public function beforeSave(Event $event, Entity $entity)
    {
        if (!$entity->has('taxonomies')) {
            return;
        }
        foreach ($entity->taxonomies as $key => $taxonomy) {
            if (!$taxonomy->has('_joinData')) {
                $taxonomy->_joinData = new Entity();
            }
            $taxonomy->_joinData->model = $entity->source();
        }

        if (isset($options['associated']) &&
            !(isset($options['associated']['Taxonomies']) || in_array('Taxonomies', $options['associated']))
        ) {
            $options['associated'][] = 'Taxonomies';
        }
    }

    public function beforeFind(Event $event, Query $query)
    {
        $query->contain(['Taxonomies']);
    }

    /**
     * Caches Term in `terms` field
     *
     * @param Model model
     * @param array $data
     * @return void
     */
    public function cacheTerms(Model $model, &$data = null)
    {
        if ($data === null) {
            $data =& $model->data;
        }
        $taxonomyIds = $this->_getSelectedTerms($data);
        $taxonomies = $model->Taxonomy->find(
            'all',
            [
                'conditions' => [
                    'Taxonomy.id' => $taxonomyIds,
                ],
            ]
        );
        $terms = Hash::combine($taxonomies, '{n}.Term.id', '{n}.Term.slug');
        $data[$model->alias]['terms'] = $model->encodeData(
            $terms,
            [
                'trim' => false,
                'json' => true,
            ]
        );
    }
}
