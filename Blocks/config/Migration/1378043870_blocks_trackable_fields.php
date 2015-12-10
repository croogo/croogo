<?php

namespace Croogo\Blocks\Config\Migration;

class BlocksTrackableFields extends CakeMigration
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
                'blocks' => [
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
                'regions' => [
                    'created' => [
                        'type' => 'datetime',
                        'null' => true,
                        'after' => 'block_count',
                    ],
                    'created_by' => [
                        'type' => 'integer',
                        'length' => 20,
                        'after' => 'created',
                    ],
                    'updated' => [
                        'type' => 'datetime',
                        'null' => true,
                        'after' => 'created_by',
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
                'blocks' => [
                    'created_by',
                    'updated_by',
                ],
                'regions' => [
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
