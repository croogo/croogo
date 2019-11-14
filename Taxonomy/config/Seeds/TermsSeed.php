<?php

use Phinx\Seed\AbstractSeed;

class TermsSeed extends AbstractSeed
{

    public $table = 'terms';

    public $records = [
        [
            'id' => '1',
            'title' => 'Uncategorized',
            'slug' => 'uncategorized',
            'description' => '',
            'created_by' => 1,
        ],
        [
            'id' => '2',
            'title' => 'Announcements',
            'slug' => 'announcements',
            'description' => '',
            'created_by' => 1,
        ],
        [
            'id' => '3',
            'title' => 'mytag',
            'slug' => 'mytag',
            'description' => '',
            'created_by' => 1,
        ],
    ];

    public function run()
    {
        $Table = $this->table('terms');
        $Table->insert($this->records)->save();
    }
}
