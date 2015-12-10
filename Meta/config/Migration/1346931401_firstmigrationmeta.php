<?php

namespace Croogo\Meta\Config\Migration;

class FirstMigrationMeta extends CakeMigration
{

/**
 * Migration description
 *
 * @var string
 * @access public
 */
    public $description = '';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
    public $migration = [
        'up' => [
            'create_table' => [
                'meta' => [
                    'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'],
                    'model' => ['type' => 'string', 'null' => false, 'default' => 'Node', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'foreign_key' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 20],
                    'key' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'value' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'weight' => ['type' => 'integer', 'null' => true, 'default' => null],
                    'indexes' => [
                        'PRIMARY' => ['column' => 'id', 'unique' => 1]
                    ],
                    'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
                ],
            ],
        ],
        'down' => [
            'drop_table' => [
                'meta'
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
        if ($direction === 'down') {
            return false;
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
