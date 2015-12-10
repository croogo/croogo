<?php
namespace Croogo\Blocks\Config\Migration;

class FirstMigrationBlocks extends CakeMigration
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
                    'blocks' => [
                    'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'],
                    'region_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 20],
                    'title' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'alias' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'key' => 'unique', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'body' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'show_title' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
                    'class' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'status' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
                    'weight' => ['type' => 'integer', 'null' => true, 'default' => null],
                    'element' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'visibility_roles' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'visibility_paths' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'visibility_php' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'params' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
                    'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
                    'indexes' => [
                        'PRIMARY' => ['column' => 'id', 'unique' => 1],
                        'block_alias' => ['column' => 'alias', 'unique' => 1]
                    ],
                    'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
                    ],
                    'regions' => [
                    'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
                    'title' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'alias' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'key' => 'unique', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'description' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'block_count' => ['type' => 'integer', 'null' => false, 'default' => '0'],
                    'indexes' => [
                        'PRIMARY' => ['column' => 'id', 'unique' => 1],
                        'region_alias' => ['column' => 'alias', 'unique' => 1]
                    ],
                    'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
                    ],
            ],
        ],
        'down' => [
            'drop_table' => [
                'blocks', 'regions'
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
