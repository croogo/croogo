<?php

namespace Croogo\Menus\Config\Migration;

class MenusTrackableFields extends CakeMigration
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
                'menus' => [
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
                'links' => [
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
                'menus' => [
                    'created_by',
                    'updated_by',
                ],
                'links' => [
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
