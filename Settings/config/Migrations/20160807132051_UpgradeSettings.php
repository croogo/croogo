<?php
use Migrations\AbstractMigration;

class UpgradeSettings extends AbstractMigration
{

    public function up()
    {

        $this->table('languages')
            ->renameColumn('updated', 'modified')
            ->update();

        $this->table('settings')
            ->renameColumn('updated', 'modified')
            ->addColumn('option_class', 'string', [
                'default' => null,
                'length' => 255,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('languages')
            ->renameColumn('modified', 'updated')
            ->update();

        $this->table('settings')
            ->renameColumn('modified', 'updated')
            ->removeColumn('option_class')
            ->update();
    }
}

