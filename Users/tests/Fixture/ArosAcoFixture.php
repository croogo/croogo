<?php

namespace Croogo\Users\Test\Fixture;

use Acl\Controller\Component\AclComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Datasource\ConnectionInterface;
use Cake\ORM\TableRegistry;
use Croogo\Core\TestSuite\CroogoTestFixture;

class ArosAcoFixture extends CroogoTestFixture
{

    public $name = 'ArosAco';

    public $fields = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null],
        'aro_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
        'aco_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
        '_create' => ['type' => 'string', 'null' => false, 'default' => '0', 'length' => 2],
        '_read' => ['type' => 'string', 'null' => false, 'default' => '0', 'length' => 2],
        '_update' => ['type' => 'string', 'null' => false, 'default' => '0', 'length' => 2],
        '_delete' => ['type' => 'string', 'null' => false, 'default' => '0', 'length' => 2],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
        '_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
    ];

    public function insert(ConnectionInterface $db)
    {
        $roles = TableRegistry::get('Croogo/Users.Roles');
        $admin = $roles->get(1);
        $registered = $roles->get(2);
        $public = $roles->get(3);

        $acl = new AclComponent(new ComponentRegistry);
        $acl->allow($admin, 'controllers');
        $acl->allow($registered, 'controllers');
        $acl->allow($public, 'controllers');

        return true;
    }
}
