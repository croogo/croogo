<?php

use Migrations\AbstractMigration;

class MetaInitialMigration extends AbstractMigration
{
    public function up()
    {

        $this->table('meta')
            ->addColumn('model', 'string', [
                'default' => 'Node',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('key', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('value', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('weight', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addTimestamps('created', 'updated')
            ->addColumn('created_by', 'integer', [
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('updated_by', 'integer', [
                'default' => null,
                'limit' => 20,
                'null' => true,
            ])
            ->create();
    }

    public function down()
    {
        $this->table('meta')->drop()->save();
    }
}
