<?php

namespace Croogo\Users\Config\Schema;

class UsersSchema extends CakeSchema
{

    public function before($event = [])
    {
        return true;
    }

    public function after($event = [])
    {
    }

    public $roles = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
        'title' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
        'alias' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'key' => 'unique', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
        'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
        'updated' => ['type' => 'datetime', 'null' => true, 'default' => null],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1],
            'role_alias' => ['column' => 'alias', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
    ];

    public $roles_users = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
        'user_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'],
        'role_id' => ['type' => 'integer', 'null' => false, 'default' => null],
        'granted_by' => ['type' => 'integer', 'null' => false, 'default' => null],
        'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
        'updated' => ['type' => 'datetime', 'null' => true, 'default' => null],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1],
            'pk_role_users' => ['column' => ['user_id', 'role_id'], 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
    ];

    public $users = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'],
        'role_id' => ['type' => 'integer', 'null' => false, 'default' => null],
        'username' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 60, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
        'password' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
        'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
        'email' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
        'website' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
        'activation_key' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 60, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
        'image' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
        'bio' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
        'timezone' => ['type' => 'string', 'null' => false, 'default' => '0', 'length' => 10, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
        'status' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
        'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
    ];
}
