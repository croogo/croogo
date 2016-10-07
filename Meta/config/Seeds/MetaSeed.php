<?php

use Phinx\Seed\AbstractSeed;

class MetaSeed extends AbstractSeed
{

    public $records = [
        [
            'id' => '1',
            'model' => 'Node',
            'foreign_key' => '1',
            'key' => 'meta_keywords',
            'value' => 'key1, key2',
            'weight' => null,
        ],
    ];

    public function run()
    {
        $Table = $this->table('meta');
        $Table->insert($this->records)->save();
    }

}
