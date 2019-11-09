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
        ],
        [
            'id' => '2',
            'title' => 'Public',
            'alias' => 'public',
        ],
        [
            'id' => '3',
            'title' => 'Registered',
            'alias' => 'registered',
            'parent_id' => 2,
        ],
        [
            'id' => '4',
            'title' => 'Admin',
            'alias' => 'admin',
            'parent_id' => 2,
        ],
        [
            'id' => '5',
            'title' => 'Publisher',
            'alias' => 'publisher',
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
