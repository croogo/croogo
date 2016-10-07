<?php

use Phinx\Seed\AbstractSeed;

class ArosSeed extends AbstractSeed
{

    public $records = [
        [
            'id' => '1',
            'parent_id' => '2',
            'model' => 'Roles',
            'foreign_key' => '1',
            'alias' => 'Role-admin',
            'lft' => '3',
            'rght' => '4'
        ],
        [
            'id' => '2',
            'parent_id' => '3',
            'model' => 'Roles',
            'foreign_key' => '2',
            'alias' => 'Role-registered',
            'lft' => '2',
            'rght' => '5'
        ],
        [
            'id' => '3',
            'parent_id' => null,
            'model' => 'Roles',
            'foreign_key' => '3',
            'alias' => 'Role-public',
            'lft' => '1',
            'rght' => '6'
        ],
    ];

    public function run()
    {
        $Table = $this->table('aros');
        $Table->insert($this->records)->save();
    }

}
