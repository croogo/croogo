<?php

use Phinx\Seed\AbstractSeed;

class TypesVocabulariesSeed extends AbstractSeed
{

    public $table = 'types_vocabularies';

    public $records = [
        [
            'id' => '31',
            'type_id' => '2',
            'vocabulary_id' => '2',
            'weight' => null,
        ],
        [
            'id' => '30',
            'type_id' => '2',
            'vocabulary_id' => '1',
            'weight' => null,
        ],
        [
            'id' => '25',
            'type_id' => '4',
            'vocabulary_id' => '2',
            'weight' => null,
        ],
        [
            'id' => '24',
            'type_id' => '4',
            'vocabulary_id' => '1',
            'weight' => null,
        ],
    ];

    public function run()
    {
        $Table = $this->table('types_vocabularies');
        $Table->insert($this->records)->save();
    }

}
