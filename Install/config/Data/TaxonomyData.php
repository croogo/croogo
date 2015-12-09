<?php
namespace Croogo\Install\Config\Data;

class TaxonomyData
{

    public $table = 'taxonomies';

    public $records = [
        [
            'id' => '1',
            'parent_id' => '',
            'term_id' => '1',
            'vocabulary_id' => '1',
            'lft' => '1',
            'rght' => '2'
        ],
        [
            'id' => '2',
            'parent_id' => '',
            'term_id' => '2',
            'vocabulary_id' => '1',
            'lft' => '3',
            'rght' => '4'
        ],
        [
            'id' => '3',
            'parent_id' => '',
            'term_id' => '3',
            'vocabulary_id' => '2',
            'lft' => '1',
            'rght' => '2'
        ],
    ];
}
