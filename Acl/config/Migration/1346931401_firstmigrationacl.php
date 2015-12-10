<?php
namespace Croogo\Acl\Config\Migration;

class FirstMigrationAcl extends CakeMigration
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
                'acos' => [
                    'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'],
                    'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
                    'model' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'foreign_key' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
                    'alias' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'lft' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
                    'rght' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
                    'indexes' => [
                        'PRIMARY' => ['column' => 'id', 'unique' => 1]
                    ],
                    'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
                ],
                'aros' => [
                    'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'],
                    'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
                    'model' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'foreign_key' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
                    'alias' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'lft' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
                    'rght' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
                    'indexes' => [
                        'PRIMARY' => ['column' => 'id', 'unique' => 1]
                    ],
                    'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
                ],
                'aros_acos' => [
                    'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'],
                    'aro_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
                    'aco_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
                    '_create' => ['type' => 'string', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    '_read' => ['type' => 'string', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    '_update' => ['type' => 'string', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    '_delete' => ['type' => 'string', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'indexes' => [
                        'PRIMARY' => ['column' => 'id', 'unique' => 1]
                    ],
                    'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
                ],
            ],
        ],
        'down' => [
            'drop_table' => [
                'acos', 'aros', 'aros_acos'
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
