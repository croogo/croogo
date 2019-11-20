<?php
use Migrations\AbstractMigration;

class FileManagerSyncTimestampFields extends AbstractMigration
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
        $this->table('assets')
            ->renameColumn('updated', 'modified')
            ->save();

        $this->table('attachments')
            ->renameColumn('updated', 'modified')
            ->renameColumn('updated_by', 'modified_by')
            ->save();

        $this->table('asset_usages')
            ->renameColumn('updated', 'modified')
            ->save();
    }
}
