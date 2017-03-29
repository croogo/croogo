<?php

use Phinx\Seed\AbstractSeed;

class LanguagesSeed extends AbstractSeed
{

    public $records = [
        [
            'id' => '1',
            'title' => 'English (United States)',
            'native' => 'English',
            'alias' => 'en',
            'locale' => 'en_US',
            'status' => '1',
            'weight' => '1',
            'updated' => '2009-11-02 21:37:38',
            'created' => '2009-11-02 20:52:00'
        ],
        [
            'id' => '2',
            'title' => 'Indonesian',
            'native' => 'Bahasa Indonesia',
            'alias' => 'id',
            'locale' => 'id_ID',
            'status' => '1',
            'weight' => '2',
            'updated' => '2017-03-29 00:00:00',
            'created' => '2017-03-29 00:00:00',
        ],
    ];

    public function run()
    {
        $Table = $this->table('languages');
        $Table->insert($this->records)->save();
    }

}
