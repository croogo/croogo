<?php

namespace Croogo\Menus\Config\Migration;

class MenusPublishingFields extends CakeMigration
{

/**
 * Migration description
 *
 * @var string
 * @access public
 */
    public $description = 'Adding/modifying publishing related fields';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
    public $migration = [
        'up' => [
            'alter_field' => [
                'menus' => [
                    'status' => [
                        'type' => 'integer',
                        'length' => 1,
                    ],
                ],
                'links' => [
                    'status' => [
                        'type' => 'integer',
                        'length' => 1,
                    ],
                ],
            ],
            'create_field' => [
                'menus' => [
                    'publish_start' => [
                        'type' => 'datetime',
                        'after' => 'params',
                    ],
                    'publish_end' => [
                        'type' => 'datetime',
                        'after' => 'publish_start',
                    ],
                ],
                'links' => [
                    'publish_start' => [
                        'type' => 'datetime',
                        'after' => 'params',
                    ],
                    'publish_end' => [
                        'type' => 'datetime',
                        'after' => 'publish_start',
                    ],
                ],
            ],
        ],
        'down' => [
            'alter_field' => [
                'menus' => [
                    'status' => [
                        'type' => 'boolean',
                    ],
                ],
                'links' => [
                    'status' => [
                        'type' => 'boolean',
                    ],
                ],
            ],
            'drop_field' => [
                'menus' => [
                    'publish_start',
                    'publish_end',
                ],
                'links' => [
                    'publish_start',
                    'publish_end',
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
        if ($direction == 'down') {
            return Configure::read('debug') > 0;
        }
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
