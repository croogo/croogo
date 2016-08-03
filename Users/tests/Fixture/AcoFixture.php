<?php

namespace Croogo\Users\Test\Fixture;

use Cake\Datasource\ConnectionInterface;
use Croogo\Acl\AclGenerator;
use Croogo\Core\TestSuite\CroogoTestFixture;

class AcoFixture extends CroogoTestFixture
{

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

    public function insert(ConnectionInterface $db)
    {
        $generator = new AclGenerator();

        return $generator->insertAcos($db);
    }
}
