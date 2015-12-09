<?php

namespace Croogo\Acl\Config\Schema;

class AclSchema extends CakeSchema
{

    public function before($event = [])
    {
        return true;
    }

    public function after($event = [])
    {
    }

    public $acos = [
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
    ];

    public $aros = [
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
    ];

    public $aros_acos = [
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
    ];
}
