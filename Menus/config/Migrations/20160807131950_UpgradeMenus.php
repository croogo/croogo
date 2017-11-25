<?php
use Migrations\AbstractMigration;

class UpgradeMenus extends AbstractMigration
{

    public function up()
    {

        $this->table('menus')
            ->changeColumn('class', 'string', [
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('menus')
            ->changeColumn('class', 'string', [
                'default' => null,
                'length' => 255,
                'null' => false,
            ])
            ->update();
    }
}

