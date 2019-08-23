<?php
use Migrations\AbstractMigration;

class MenusAddForeignKeys extends AbstractMigration
{

    public function up()
    {
        $this->table('links')
            ->addForeignKey('menu_id', 'menus', ['id'], [
                'constraint' => 'fk_links2menus',
                'delete' => 'RESTRICT',
            ])
            ->save();
    }

    public function down()
    {
        $this->table('links')
            ->dropForeignKey('menu_id')
            ->save();
    }

}
