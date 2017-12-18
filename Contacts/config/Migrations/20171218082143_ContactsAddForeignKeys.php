<?php
use Migrations\AbstractMigration;

class ContactsAddForeignKeys extends AbstractMigration
{

    public function up()
    {
        $this->table('messages')
            ->addForeignKey('contact_id', 'contacts', ['id'], [
                'constraint' => 'fk_messages2contacts',
                'delete' => 'RESTRICT',
            ])
            ->save();
    }

    public function down()
    {
        $this->table('messages')
            ->dropForeignKey('contact_id')
            ->save();
    }

}
