<?php

namespace Croogo\Taxonomy\Model\Table;

use Croogo\Core\Model\Table\CroogoTable;

class TypesTable extends CroogoTable
{

/**
 * Display fields for this model
 *
 * @var array
 */
    protected $_displayFields = [
        'title' => [
            'url' => [
                'prefix' => false,
                'plugin' => 'Croogo/Nodes',
                'controller' => 'Nodes',
                'action' => 'index',
                'named' => [
                    'alias' => 'type'
                ],
            ]
        ],
        'description',
        'plugin',
    ];

    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always'
                ]
            ]
        ]);
        $this->addBehavior('Croogo/Core.Url', [
            'url' => [
                'plugin' => 'Croogo/Nodes',
                'controller' => 'Nodes',
                'action' => 'index'
            ],
            'fields' => [
                'type' => 'alias'
            ]
        ]);
        $this->belongsToMany('Croogo/Taxonomy.Vocabularies', [
            'joinTable' => 'types_vocabularies',
        ]);
    }

/**
 * Get a list of relevant types for given plugin
 */
    public function pluginTypes($plugin = null)
    {
        if ($plugin === null) {
            $conditions = [];
        } elseif ($plugin) {
            $conditions = ['plugin' => $plugin];
        } else {
            $conditions = [
                'OR' => [
                    'plugin LIKE' => '',
                    'plugin' => null,
                ],
            ];
        }
        return $this->find('list', compact('conditions'));
    }
}
