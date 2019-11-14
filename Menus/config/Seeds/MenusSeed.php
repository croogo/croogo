<?php

use Phinx\Seed\AbstractSeed;

class MenusSeed extends AbstractSeed
{

    public $records = [
        [
            'id' => '3',
            'title' => 'Main Menu',
            'alias' => 'main',
            'class' => '',
            'description' => '',
            'status' => '1',
            'weight' => null,
            'link_count' => '4',
            'params' => '',
            'created_by' => 1,
        ],
        [
            'id' => '4',
            'title' => 'Footer',
            'alias' => 'footer',
            'class' => '',
            'description' => '',
            'status' => '1',
            'weight' => null,
            'link_count' => '2',
            'params' => '',
            'created_by' => 1,
        ],
        [
            'id' => '5',
            'title' => 'Meta',
            'alias' => 'meta',
            'class' => '',
            'description' => '',
            'status' => '1',
            'weight' => null,
            'link_count' => '4',
            'params' => '',
            'created_by' => 1,
        ],
        [
            'id' => '6',
            'title' => 'Blogroll',
            'alias' => 'blogroll',
            'class' => '',
            'description' => '',
            'status' => '1',
            'weight' => null,
            'link_count' => '2',
            'params' => '',
            'created_by' => 1,
        ],
    ];

    public function run()
    {
        $Table = $this->table('menus');
        $Table->insert($this->records)->save();
    }
}
