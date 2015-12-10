<?php

namespace Croogo\Meta\Config\Migration;

class MetaTrackableFields extends CakeMigration
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
                'meta' => [
                    'created' => [
                        'type' => 'datetime',
                        'after' => 'weight',
                        'null' => true,
                    ],
                    'created_by' => [
                        'type' => 'integer',
                        'length' => 20,
                        'after' => 'created',
                    ],
                    'updated' => [
                        'type' => 'datetime',
                        'after' => 'created_by',
                        'null' => true,
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
                'meta' => [
                    'created',
                    'created_by',
                    'updated',
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
