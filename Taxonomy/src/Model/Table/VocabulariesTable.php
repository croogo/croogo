<?php

namespace Croogo\Taxonomy\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;
use Croogo\Core\Model\Table\CroogoTable;

/**
 * @property TaxonomiesTable Taxonomies
 */
class VocabulariesTable extends CroogoTable
{

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->addBehavior('ADmad/Sequence.Sequence', [
            'order' => 'weight',
        ]);

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always'
                ]
            ]
        ]);
        $this->addBehavior('Search.Search');
        $this->addBehavior('Croogo/Core.Cached', [
            'groups' => ['taxonomy']
        ]);
        $this->belongsToMany('Croogo/Taxonomy.Types', [
            'joinTable' => 'types_vocabularies',
        ]);
        $this->hasMany('Croogo/Taxonomy.Taxonomies', [
            'dependent' => true,
        ]);
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->notBlank('title', __d('croogo', 'The title cannot be empty'))
            ->notBlank('alias', __d('croogo', 'The alias cannot be empty'));

        return parent::validationDefault($validator);
    }

    /**
     * @param \Cake\ORM\RulesChecker $rules
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(
            ['alias'],
            __d('croogo', 'That alias is already taken')
        ));
        return parent::buildRules($rules);
    }
}
