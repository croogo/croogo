<?php
use Migrations\AbstractMigration;

class BlocksSyncTimestampFields extends AbstractMigration
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
        $this->table('regions')
            ->renameColumn('updated', 'modified')
            ->renameColumn('updated_by', 'modified_by')
            ->update();

        $this->table('blocks')
            ->renameColumn('updated', 'modified')
            ->renameColumn('updated_by', 'modified_by')
            ->update();
    }
}
