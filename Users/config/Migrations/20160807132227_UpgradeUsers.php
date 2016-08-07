<?php
use Migrations\AbstractMigration;

class UpgradeUsers extends AbstractMigration
{

    public function up()
    {

        $this->table('users')
            ->changeColumn('password', 'string', [
                'null' => true
            ])
            ->update();
    }

    public function down()
    {

        $this->table('users')
            ->changeColumn('password', 'string', [
                'default' => null,
                'length' => 100,
                'null' => false,
            ])
            ->update();
    }
}

