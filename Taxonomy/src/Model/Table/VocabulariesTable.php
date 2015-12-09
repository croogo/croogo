<?php

namespace Croogo\Taxonomy\Model\Table;

use Croogo\Core\Model\Table\CroogoTable;

/**
 * @property TaxonomiesTable Taxonomies
 */
class VocabulariesTable extends CroogoTable
{

    public function initialize(array $config)
    {
        $this->addBehavior('Sequence.Sequence', [
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

        $this->belongsToMany('Croogo/Taxonomy.Types', [
            'joinTable' => 'types_vocabularies',
        ]);
        $this->hasMany('Croogo/Taxonomy.Taxonomies', [
            'dependent' => true,
        ]);
    }
}
