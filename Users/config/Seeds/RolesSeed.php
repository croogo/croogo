<?php

use Phinx\Seed\AbstractSeed;

class RolesSeed extends AbstractSeed
{

    public $records = [
        [
            'id' => '1',
            'title' => 'Admin',
            'alias' => 'admin',
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
    ];

    public function run()
    {
        $Table = $this->table('roles');
        $Table->insert($this->records)->save();
    }

}
