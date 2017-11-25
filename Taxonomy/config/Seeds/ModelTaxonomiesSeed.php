<?php

use Phinx\Seed\AbstractSeed;

class ModelTaxonomiesSeed extends AbstractSeed
{

    public $table = 'model_taxonomies';

    public $records = [
        [
            'id' => '1',
            'model' => 'Croogo/Nodes.Nodes',
            'foreign_key' => '1',
            'taxonomy_id' => '1'
        ],
    ];

    public function run()
    {
        $Table = $this->table('model_taxonomies');
        $Table->insert($this->records)->save();
    }

}
