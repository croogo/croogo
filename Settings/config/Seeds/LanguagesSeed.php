<?php

use Phinx\Seed\AbstractSeed;

class LanguagesSeed extends AbstractSeed
{

    public $records = [
        [
            'id' => '1',
            'title' => 'English',
            'native' => 'English',
            'alias' => 'eng',
            'status' => '1',
            'weight' => '1',
            'updated' => '2009-11-02 21:37:38',
            'created' => '2009-11-02 20:52:00'
        ],
    ];

    public function run()
    {
        $Table = $this->table('languages');
        $Table->insert($this->records)->save();
    }

}
