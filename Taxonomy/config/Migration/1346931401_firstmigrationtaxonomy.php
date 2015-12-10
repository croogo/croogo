<?php

namespace Croogo\Taxonomy\Config\Migration;

class FirstMigrationTaxonomy extends CakeMigration
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
                'taxonomies' => [
                    'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'],
                    'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 20],
                    'term_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
                    'vocabulary_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
                    'lft' => ['type' => 'integer', 'null' => true, 'default' => null],
                    'rght' => ['type' => 'integer', 'null' => true, 'default' => null],
                    'indexes' => [
                        'PRIMARY' => ['column' => 'id', 'unique' => 1]
                    ],
                    'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
                ],
                'terms' => [
                    'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'],
                    'title' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'slug' => ['type' => 'string', 'null' => false, 'default' => null, 'key' => 'unique', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'description' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
                    'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
                    'indexes' => [
                        'PRIMARY' => ['column' => 'id', 'unique' => 1],
                        'slug' => ['column' => 'slug', 'unique' => 1]
                    ],
                    'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
                ],
                'types' => [
                    'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'],
                    'title' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'alias' => ['type' => 'string', 'null' => false, 'default' => null, 'key' => 'unique', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'description' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'format_show_author' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
                    'format_show_date' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
                    'comment_status' => ['type' => 'integer', 'null' => false, 'default' => '1', 'length' => 1],
                    'comment_approve' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
                    'comment_spam_protection' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
                    'comment_captcha' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
                    'params' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'plugin' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
                    'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
                    'indexes' => [
                        'PRIMARY' => ['column' => 'id', 'unique' => 1],
                        'type_alias' => ['column' => 'alias', 'unique' => 1]
                    ],
                    'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
                ],
                'types_vocabularies' => [
                    'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'],
                    'type_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
                    'vocabulary_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
                    'weight' => ['type' => 'integer', 'null' => true, 'default' => null],
                    'indexes' => [
                        'PRIMARY' => ['column' => 'id', 'unique' => 1]
                    ],
                    'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
                ],
                'vocabularies' => [
                    'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'],
                    'title' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'alias' => ['type' => 'string', 'null' => false, 'default' => null, 'key' => 'unique', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'description' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'required' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
                    'multiple' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
                    'tags' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
                    'plugin' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
                    'weight' => ['type' => 'integer', 'null' => true, 'default' => null],
                    'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
                    'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
                    'indexes' => [
                        'PRIMARY' => ['column' => 'id', 'unique' => 1],
                        'vocabulary_alias' => ['column' => 'alias', 'unique' => 1]
                    ],
                    'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
                ],
            ],
        ],
        'down' => [
            'drop_table' => [
                'taxonomies', 'terms', 'types', 'types_vocabularies', 'vocabularies'
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
