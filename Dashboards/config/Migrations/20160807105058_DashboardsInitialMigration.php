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
            ->addColumn('updated', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();
    }

    public function down()
    {
        $this->dropTable('dashboards');
    }
}
