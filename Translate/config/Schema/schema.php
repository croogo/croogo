<?php

namespace Croogo\Translate\Config\Schema;

class TranslateSchema extends CakeSchema
{

    public function before($event = [])
    {
        return true;
    }

    public function after($event = [])
    {
    }

    public $i18n = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'],
        'locale' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 6, 'key' => 'index', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
        'model' => ['type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
        'foreign_key' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'],
        'field' => ['type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
        'content' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1],
            'locale' => ['column' => 'locale', 'unique' => 0],
            'model' => ['column' => 'model', 'unique' => 0],
            'row_id' => ['column' => 'foreign_key', 'unique' => 0],
            'field' => ['column' => 'field', 'unique' => 0]
        ],
        'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
    ];
}
