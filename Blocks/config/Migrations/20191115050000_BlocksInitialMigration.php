<?php

use Migrations\AbstractMigration;

class BlocksInitialMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('regions')
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('alias', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('block_count', 'integer', [
                'default' => 0,
                'limit' => 11,
                'null' => false,
            ])
            ->addTimestamps('created', 'modified')
            ->addColumn('modified_by', 'integer', [
                'default' => null,
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('created_by', 'integer', [
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addIndex(
                [
                    'alias',
                ],
                ['unique' => true]
            )
            ->create();

        $this->table('blocks')
            ->addColumn('region_id', 'integer', [
                'default' => null,
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('alias', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('body', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('show_title', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('class', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('status', 'integer', [
                'default' => null,
                'limit' => 1,
                'null' => true,
            ])
            ->addColumn('weight', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('element', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('visibility_roles', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('visibility_paths', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('visibility_php', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('params', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('publish_start', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('publish_end', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addTimestamps('created', 'modified')
            ->addColumn('created_by', 'integer', [
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('modified_by', 'integer', [
                'default' => null,
                'limit' => 20,
                'null' => true,
            ])
            ->addForeignKey('region_id', 'regions', ['id'], [
                'constraint' => 'fk_blocks2regions',
                'delete' => 'RESTRICT',
            ])
            ->addIndex(
                [
                    'alias',
                ],
                ['unique' => true]
            )
            ->create();
    }

    public function down()
    {
        $this->table('blocks')->drop()->save();
        $this->table('regions')->drop()->save();
    }
}
