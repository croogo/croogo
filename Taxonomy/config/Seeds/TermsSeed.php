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
        ],
        [
            'id' => '2',
            'title' => 'Announcements',
            'slug' => 'announcements',
            'description' => '',
        ],
        [
            'id' => '3',
            'title' => 'mytag',
            'slug' => 'mytag',
            'description' => '',
        ],
    ];

    public function run()
    {
        $Table = $this->table('terms');
        $Table->insert($this->records)->save();
    }
}
