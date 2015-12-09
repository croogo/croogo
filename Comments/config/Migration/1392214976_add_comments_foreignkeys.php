<?php

namespace Croogo\Comments\Config\Migration;

class AddCommentsForeignKeys extends CakeMigration
{

/**
 * Migration description
 *
 * @var string
 * @access public
 */
    public $description = 'Add comments foreign keys';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
    public $migration = [
        'up' => [
            'create_field' => [
                'comments' => [
                    'model' => [
                        'type' => 'string',
                        'length' => 50,
                        'after' => 'parent_id',
                        'null' => false,
                        'default' => 'Node',
                    ],
                    'indexes' => [
                        'comments_fk' => [
                            'column' => ['model', 'foreign_key'],
                        ],
                    ],
                ],
            ],
            'rename_field' => [
                'comments' => [
                    'node_id' => 'foreign_key',
                ],
            ],
        ],
        'down' => [
            'drop_field' => [
                'comments' => [
                    'model',
                    'indexes' => ['comments_fk'],
                ],
            ],
            'rename_field' => [
                'comments' => [
                    'foreign_key' => 'node_id',
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
