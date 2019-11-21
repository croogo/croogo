<?php
use Migrations\AbstractMigration;

class TaxonomySyncTimestampFields extends AbstractMigration
{

    public function up()
    {
        $this->table('terms')
            ->renameColumn('updated', 'modified')
            ->renameColumn('updated_by', 'modified_by')
            ->update();

        $this->table('vocabularies')
            ->renameColumn('updated', 'modified')
            ->renameColumn('updated_by', 'modified_by')
            ->update();

        $this->table('taxonomies')
            ->addTimestamps('updated', 'modified')
            ->update();

        $this->table('types')
            ->renameColumn('updated', 'modified')
            ->renameColumn('updated_by', 'modified_by')
            ->update();

        $this->table('types_vocabularies')
            ->addTimestamps('updated', 'modified')
            ->update();

        $this->table('model_taxonomies')
            ->addTimestamps('updated', 'modified')
            ->update();
    }

    public function down()
    {
        $this->table('terms')
            ->renameColumn('modified', 'update')
            ->renameColumn('modified_by', 'updated_by')
            ->update();

        $this->table('vocabularies')
            ->renameColumn('modified', 'update')
            ->renameColumn('modified_by', 'updated_by')
            ->update();

        $this->table('taxonomies')
            ->removeColumn('updated')
            ->removeColumn('modified')
            ->update();

        $this->table('types')
            ->renameColumn('updated', 'modified')
            ->renameColumn('updated_by', 'modified_by')
            ->update();

        $this->table('types_vocabularies')
            ->removeColumn('updated')
            ->removeColumn('modified')
            ->update();

        $this->table('model_taxonomies')
            ->removeColumn('updated')
            ->removeColumn('modified')
            ->update();
    }

}
