<?php

namespace Croogo\Taxonomy\Config\Migration;

class TaxonomyTrackableFields extends CakeMigration
{

/**
 * Migration description
 *
 * @var string
 * @access public
 */
    public $description = 'Adding Trackable Fields';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
    public $migration = [
        'up' => [
            'create_field' => [
                'terms' => [
                    'created_by' => [
                        'type' => 'integer',
                        'length' => 20,
                        'after' => 'created',
                    ],
                    'updated_by' => [
                        'type' => 'integer',
                        'length' => 20,
                        'after' => 'updated',
                    ],
                ],
                'types' => [
                    'created_by' => [
                        'type' => 'integer',
                        'length' => 20,
                        'after' => 'created',
                    ],
                    'updated_by' => [
                        'type' => 'integer',
                        'length' => 20,
                        'after' => 'updated',
                    ],
                ],
                'vocabularies' => [
                    'created_by' => [
                        'type' => 'integer',
                        'length' => 20,
                        'after' => 'created',
                    ],
                    'updated_by' => [
                        'type' => 'integer',
                        'length' => 20,
                        'after' => 'updated',
                    ],
                ],
            ],
        ],
        'down' => [
            'drop_field' => [
                'terms' => [
                    'created_by',
                    'updated_by',
                ],
                'types' => [
                    'created_by',
                    'updated_by',
                ],
                'vocabularies' => [
                    'created_by',
                    'updated_by',
                ],
            ],
        ],
    ];

/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
    public function before($direction)
    {
        return true;
    }

/**
 * After migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
    public function after($direction)
    {
        return true;
    }
}
