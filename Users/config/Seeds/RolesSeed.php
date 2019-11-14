<?php

use Cake\ORM\TableRegistry;
use Phinx\Seed\AbstractSeed;

class RolesSeed extends AbstractSeed
{

    public $records = [
        [
            'id' => '1',
            'title' => 'SuperAdmin',
            'alias' => 'superadmin',
            'created_by' => 1,
        ],
        [
            'id' => '2',
            'title' => 'Public',
            'alias' => 'public',
            'created_by' => 1,
        ],
        [
            'id' => '3',
            'title' => 'Registered',
            'alias' => 'registered',
            'created_by' => 1,
            'parent_id' => 2,
        ],
        [
            'id' => '4',
            'title' => 'Admin',
            'alias' => 'admin',
            'created_by' => 1,
            'parent_id' => 2,
        ],
        [
            'id' => '5',
            'title' => 'Publisher',
            'alias' => 'publisher',
            'created_by' => 1,
            'parent_id' => 4,
        ],
    ];

    public function run()
    {
        $this->getAdapter()->commitTransaction();
        $Roles = TableRegistry::get('Croogo/Users.Roles');
        $entities = $Roles->newEntities($this->records);
        $result = $Roles->saveMany($entities);
        $this->getAdapter()->beginTransaction();
    }
}
