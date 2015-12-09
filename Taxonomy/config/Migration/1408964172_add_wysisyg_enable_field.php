<?php
class AddWysisygEnableField extends CakeMigration
{

/**
 * Migration description
 *
 * @var string
 */
    public $description = '';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
    public $migration = [
        'up' => [
            'create_field' => [
                'types' => [
                    'format_use_wysiwyg' => ['type' => 'boolean', 'null' => false, 'default' => '1', 'after' => 'format_show_date'],
                ],
            ],
        ],
        'down' => [
            'drop_field' => [
                'types' => ['format_use_wysiwyg',],
            ],
        ],
    ];

/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
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
 */
    public function after($direction)
    {
        return true;
    }
}
