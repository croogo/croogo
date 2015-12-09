<?php

namespace Croogo\Nodes\Config\Migration;

class FirstMigrationNodes extends CakeMigration
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
                'nodes' => [
                    'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'],
                    'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 20],
                    'user_id' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20],
                    'title' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'slug' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'body' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'excerpt' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'status' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
                    'mime_type' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'comment_status' => ['type' => 'integer', 'null' => false, 'default' => '1', 'length' => 1],
                    'comment_count' => ['type' => 'integer', 'null' => true, 'default' => '0'],
                    'promote' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
                    'path' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'terms' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'sticky' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
                    'lft' => ['type' => 'integer', 'null' => true, 'default' => null],
                    'rght' => ['type' => 'integer', 'null' => true, 'default' => null],
                    'visibility_roles' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'type' => ['type' => 'string', 'null' => false, 'default' => 'node', 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
                    'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
                    'indexes' => [
                        'PRIMARY' => ['column' => 'id', 'unique' => 1]
                    ],
                    'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
                ],
                'nodes_taxonomies' => [
                    'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'],
                    'node_id' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20],
                    'taxonomy_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20],
                    'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
                    'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
                ],
            ],
        ],
        'down' => [
            'drop_table' => [
                'nodes', 'nodes_taxonomies'
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
