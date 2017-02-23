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
            'created' => '2009-04-05 00:10:34',
            'updated' => '2009-04-05 00:10:34'
        ],
        [
            'id' => '2',
            'title' => 'Registered',
            'alias' => 'registered',
            'created' => '2009-04-05 00:10:50',
            'updated' => '2009-04-06 05:20:38'
        ],
        [
            'id' => '3',
            'title' => 'Public',
            'alias' => 'public',
            'created' => '2009-04-05 00:12:38',
            'updated' => '2009-04-07 01:41:45'
        ],
        [
            'id' => '4',
            'title' => 'Admin',
            'alias' => 'admin',
            'parent_id' => 2,
            'created' => '2017-01-18 01:39:00',
            'updated' => '2017-01-18 01:39:00',
        ],
        [
            'id' => '5',
            'title' => 'Publisher',
            'alias' => 'publisher',
            'parent_id' => 4,
            'created' => '2017-01-18 01:39:00',
            'updated' => '2017-01-18 01:39:00',
        ],
    ];

    public function run()
    {
        $Roles = TableRegistry::get('Croogo/Users.Roles');
        $entities = $Roles->newEntities($this->records);
        $result = $Roles->saveMany($entities);
    }

}
