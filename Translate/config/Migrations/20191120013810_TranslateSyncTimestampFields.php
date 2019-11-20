<?php
use Migrations\AbstractMigration;

class TranslateSyncTimestampFields extends AbstractMigration
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
        $this->table('i18n')
            ->renameColumn('updated', 'modified')
            ->renameColumn('updated_by', 'modified_by')
            ->save();
    }
}
