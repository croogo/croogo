<?php
use Migrations\AbstractMigration;

class TaxonomyAddForeignKeys extends AbstractMigration
{

    public function up()
    {
        $this->table('taxonomies')
            ->addForeignKey('term_id', 'terms', ['id'], [
                'constraint' => 'fk_taxonomies2terms',
                'delete' => 'RESTRICT',
            ])
            ->addForeignKey('vocabulary_id', 'vocabularies', ['id'], [
                'constraint' => 'fk_taxonomies2vocabularies',
                'delete' => 'RESTRICT',
            ])
            ->save();

        $this->table('model_taxonomies')
            ->addForeignKey('taxonomy_id', 'taxonomies', ['id'], [
                'constraint' => 'fk_model_taxonomies2taxonomies',
                'update' => 'CASCADE',
                'delete' => 'CASCADE',
            ])
            ->save();
    }

    public function down()
    {
        $this->table('taxonomies')
            ->dropForeignKey('term_id')
            ->dropForeignKey('vocabulary_id')
            ->save();

        $this->table('model_taxonomies')
            ->dropForeignKey('taxonomy_id')
            ->save();
    }

}
