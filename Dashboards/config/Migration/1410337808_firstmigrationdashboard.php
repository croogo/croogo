<?php
class FirstMigrationDashboard extends CakeMigration
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
                'dashboards' => [
                    'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'],
                    'alias' => ['type' => 'string', 'null' => false, 'default' => '', 'length' => 50],
                    'user_id' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20],
                    'column' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20],
                    'weight' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20],
                    'collapsed' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
                    'status' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
                    'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
                    'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
                    'indexes' => [
                        'PRIMARY' => ['column' => 'id', 'unique' => 1]
                    ],
                    'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
                ],
            ],
        ],
        'down' => [
            'drop_table' => [
                'dashboards'
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
