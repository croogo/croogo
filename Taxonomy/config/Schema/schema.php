<?php

namespace Croogo\Taxonomy\Config\Schema;

class TaxonomySchema extends CakeSchema
{

    public function before($event = [])
    {
        return true;
    }

    public function after($event = [])
    {
    }

    public $taxonomies = [
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
    ];

    public $terms = [
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
    ];

    public $types = [
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
    ];

    public $types_vocabularies = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'],
        'type_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
        'vocabulary_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
        'weight' => ['type' => 'integer', 'null' => true, 'default' => null],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
    ];

    public $vocabularies = [
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
    ];
}
