<?php

namespace Croogo\Meta\Config\Schema;

class MetaSchema extends CakeSchema
{

    public function before($event = [])
    {
        return true;
    }

    public function after($event = [])
    {
    }

    public $meta = [
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
    ];
}
