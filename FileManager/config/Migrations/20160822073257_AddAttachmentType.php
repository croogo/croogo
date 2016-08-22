<?php
use Migrations\AbstractMigration;

class AddAttachmentType extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function up()
    {
        $this->insert('types', [
            'title' => 'Attachment',
            'alias' => 'attachment',
            'plugin' => 'Croogo/FileManager',
            'updated' => date('Y-m-d H:i:s'),
            'created' => date('Y-m-d H:i:s'),
        ]);
    }
}
