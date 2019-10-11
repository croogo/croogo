<?php

use Migrations\AbstractMigration;

class DashboardsAddForeignKeys extends AbstractMigration
{

    public function up()
    {
        $this->table('dashboards')
            ->addForeignKey('user_id', 'users', ['id'], [
                'constraint' => 'fk_dashboards2users',
                'delete' => 'RESTRICT',
            ])
            ->save();
    }

    public function down()
    {
        $this->table('dashboards')
            ->dropForeignKey('user_id')
            ->save();
    }
}
