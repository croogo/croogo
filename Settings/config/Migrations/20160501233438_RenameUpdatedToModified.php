<?php
use Migrations\AbstractMigration;

class RenameUpdatedToModified extends AbstractMigration
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
        $languages = $this->table('languages');
        $languages
            ->renameColumn('updated', 'modified')
            ->changeColumn('modified', 'timestamp')
            ->update();

        $settings = $this->table('settings');
        $settings
            ->renameColumn('updated', 'modified')
            ->changeColumn('modified', 'timestamp')
            ->update();
    }
}
