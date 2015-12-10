<?php

namespace Croogo\Blocks\Test\Fixture;

class RegionFixture extends CroogoTestFixture
{

    public $name = 'Region';

    public $fields = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null],
        'title' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100],
        'alias' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100],
        'description' => ['type' => 'text', 'null' => false, 'default' => null],
        'block_count' => ['type' => 'integer', 'null' => false, 'default' => '0'],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
            'region_alias' => ['type' => 'unique', 'columns' => 'alias']
        ],
        '_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
    ];

    public $records = [
        [
            'id' => 3,
            'title' => 'none',
            'alias' => '',
            'description' => '',
            'block_count' => 0
        ],
        [
            'id' => 4,
            'title' => 'right',
            'alias' => 'right',
            'description' => '',
            'block_count' => 6
        ],
        [
            'id' => 6,
            'title' => 'left',
            'alias' => 'left',
            'description' => '',
            'block_count' => 0
        ],
        [
            'id' => 7,
            'title' => 'header',
            'alias' => 'header',
            'description' => '',
            'block_count' => 0
        ],
        [
            'id' => 8,
            'title' => 'footer',
            'alias' => 'footer',
            'description' => '',
            'block_count' => 0
        ],
        [
            'id' => 9,
            'title' => 'region1',
            'alias' => 'region1',
            'description' => '',
            'block_count' => 0
        ],
        [
            'id' => 10,
            'title' => 'region2',
            'alias' => 'region2',
            'description' => '',
            'block_count' => 0
        ],
        [
            'id' => 11,
            'title' => 'region3',
            'alias' => 'region3',
            'description' => '',
            'block_count' => 0
        ],
        [
            'id' => 12,
            'title' => 'region4',
            'alias' => 'region4',
            'description' => '',
            'block_count' => 0
        ],
        [
            'id' => 13,
            'title' => 'region5',
            'alias' => 'region5',
            'description' => '',
            'block_count' => 0
        ],
        [
            'id' => 14,
            'title' => 'region6',
            'alias' => 'region6',
            'description' => '',
            'block_count' => 0
        ],
        [
            'id' => 15,
            'title' => 'region7',
            'alias' => 'region7',
            'description' => '',
            'block_count' => 0
        ],
        [
            'id' => 16,
            'title' => 'region8',
            'alias' => 'region8',
            'description' => '',
            'block_count' => 0
        ],
        [
            'id' => 17,
            'title' => 'region9',
            'alias' => 'region9',
            'description' => '',
            'block_count' => 0
        ],
    ];
}
