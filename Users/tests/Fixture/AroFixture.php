<?php

namespace Croogo\Users\Test\Fixture;

use Croogo\Core\TestSuite\CroogoTestFixture;

class AroFixture extends CroogoTestFixture
{

    public $name = 'Aro';

    public $fields = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null],
        'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
        'model' => ['type' => 'string', 'null' => true],
        'foreign_key' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
        'alias' => ['type' => 'string', 'null' => true],
        'lft' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
        'rght' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
        '_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
    ];

    public $records = [
        [
            'id' => 1,
            'parent_id' => 2,
            'model' => 'Roles',
            'foreign_key' => 1,
            'alias' => 'Role-admin',
            'lft' => 3,
            'rght' => 8,
        ],
        [
            'id' => 2,
            'parent_id' => 3,
            'model' => 'Roles',
            'foreign_key' => 2,
            'alias' => 'Role-registered',
            'lft' => 2,
            'rght' => 11,
        ],
        [
            'id' => 3,
            'parent_id' => null,
            'model' => 'Roles',
            'foreign_key' => 3,
            'alias' => 'Role-public',
            'lft' => 1,
            'rght' => 12,
        ],
        [
            'id' => 4,
            'parent_id' => 1,
            'model' => 'Users',
            'foreign_key' => 1,
            'alias' => 'admin',
            'lft' => 4,
            'rght' => 5
        ],
        [
            'id' => 5,
            'parent_id' => 1,
            'model' => 'Users',
            'foreign_key' => 2,
            'alias' => 'rchavik',
            'lft' => 6,
            'rght' => 7,
        ],
        [
            'id' => 6,
            'parent_id' => 3,
            'model' => 'Users',
            'foreign_key' => 3,
            'alias' => 'yvonne',
            'lft' => 9,
            'rght' => 10,
        ],
        [
            'id' => 7,
            'parent_id' => 2,
            'model' => 'Users',
            'foreign_key' => 4,
            'alias' => 'registered-user',
            'lft' => 10,
            'rght' => 12,
        ],
    ];
}
