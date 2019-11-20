<?php
use Migrations\AbstractMigration;

class TaxonomySyncTimestampFields extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
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
            ->renameColumn('updated', 'modified')
            ->update();

        $this->table('types')
            ->renameColumn('updated', 'modified')
            ->renameColumn('updated_by', 'modified_by')
            ->update();

        $this->table('types_vocabularies')
            ->renameColumn('updated', 'modified')
            ->update();

        $this->table('model_taxonomies')
            ->renameColumn('updated', 'modified')
            ->update();
    }
}
