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
            'updated' => '2010-05-17 20:03:11',
            'created' => '2009-07-22 02:16:21'
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
            'updated' => '2010-05-17 20:03:11',
            'created' => '2009-07-22 02:16:34'
        ],
    ];

    public function run()
    {
        $Table = $this->table('vocabularies');
        $Table->insert($this->records)->save();
    }

}
