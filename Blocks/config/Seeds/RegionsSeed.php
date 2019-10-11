<?php

use Phinx\Seed\AbstractSeed;

class RegionsSeed extends AbstractSeed
{

    public $records = [
        [
            'id' => '3',
            'title' => 'none',
            'alias' => 'none',
            'description' => '',
            'block_count' => '0'
        ],
        [
            'id' => '4',
            'title' => 'right',
            'alias' => 'right',
            'description' => '',
            'block_count' => '6'
        ],
        [
            'id' => '6',
            'title' => 'left',
            'alias' => 'left',
            'description' => '',
            'block_count' => '0'
        ],
        [
            'id' => '7',
            'title' => 'header',
            'alias' => 'header',
            'description' => '',
            'block_count' => '0'
        ],
        [
            'id' => '8',
            'title' => 'footer',
            'alias' => 'footer',
            'description' => '',
            'block_count' => '0'
        ],
        [
            'id' => '9',
            'title' => 'region1',
            'alias' => 'region1',
            'description' => '',
            'block_count' => '0'
        ],
        [
            'id' => '10',
            'title' => 'region2',
            'alias' => 'region2',
            'description' => '',
            'block_count' => '0'
        ],
        [
            'id' => '11',
            'title' => 'region3',
            'alias' => 'region3',
            'description' => '',
            'block_count' => '0'
        ],
        [
            'id' => '12',
            'title' => 'region4',
            'alias' => 'region4',
            'description' => '',
            'block_count' => '0'
        ],
        [
            'id' => '13',
            'title' => 'region5',
            'alias' => 'region5',
            'description' => '',
            'block_count' => '0'
        ],
        [
            'id' => '14',
            'title' => 'region6',
            'alias' => 'region6',
            'description' => '',
            'block_count' => '0'
        ],
        [
            'id' => '15',
            'title' => 'region7',
            'alias' => 'region7',
            'description' => '',
            'block_count' => '0'
        ],
        [
            'id' => '16',
            'title' => 'region8',
            'alias' => 'region8',
            'description' => '',
            'block_count' => '0'
        ],
        [
            'id' => '17',
            'title' => 'region9',
            'alias' => 'region9',
            'description' => '',
            'block_count' => '0'
        ],
    ];

    public function run()
    {
        $Table = $this->table('regions');
        $Table->insert($this->records)->save();
    }
}
