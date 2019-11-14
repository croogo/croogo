<?php

use Phinx\Seed\AbstractSeed;

class VocabulariesSeed extends AbstractSeed
{

    public $table = 'vocabularies';

    public $records = [
        [
            'id' => '1',
            'title' => 'Categories',
            'alias' => 'categories',
            'description' => '',
            'required' => '0',
            'multiple' => '1',
            'tags' => '0',
            'plugin' => null,
            'weight' => '1',
            'created_by' => 1,
        ],
        [
            'id' => '2',
            'title' => 'Tags',
            'alias' => 'tags',
            'description' => '',
            'required' => '0',
            'multiple' => '1',
            'tags' => '0',
            'plugin' => null,
            'weight' => '2',
            'created_by' => 1,
        ],
    ];

    public function run()
    {
        $Table = $this->table('vocabularies');
        $Table->insert($this->records)->save();
    }
}
