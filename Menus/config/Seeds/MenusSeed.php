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
            'updated' => '2009-08-19 12:21:06',
            'created' => '2009-07-22 01:49:53'
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
            'updated' => '2009-08-19 12:22:42',
            'created' => '2009-08-19 12:22:42'
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
            'updated' => '2009-09-12 06:33:29',
            'created' => '2009-09-12 06:33:29'
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
            'updated' => '2009-09-12 23:30:24',
            'created' => '2009-09-12 23:30:24'
        ],
    ];

    public function run()
    {
        $Table = $this->table('menus');
        $Table->insert($this->records)->save();
    }

}
