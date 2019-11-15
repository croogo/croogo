<?php

use Migrations\AbstractMigration;

class DashboardsInitialMigration extends AbstractMigration
{
    public function up()
    {

        $this->table('dashboards')
            ->addColumn('alias', 'string', [
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => 0,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('column', 'integer', [
                'default' => 0,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('weight', 'integer', [
                'default' => 0,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('collapsed', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('status', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => false,
            ])
            ->addTimestamps('created', 'updated')
            ->addForeignKey('user_id', 'users', ['id'], [
                'constraint' => 'fk_dashboards2users',
                'delete' => 'RESTRICT',
            ])
            ->create();
    }

    public function down()
    {
        $this->table('dashboards')->drop()->save();
    }
}
