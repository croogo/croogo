<?php

use Migrations\AbstractMigration;

class BlocksAddForeignKeys extends AbstractMigration
{

    public function up()
    {
        $this->table('blocks')
            ->addForeignKey('region_id', 'regions', ['id'], [
                'constraint' => 'fk_blocks2regions',
                'delete' => 'RESTRICT',
            ])
            ->save();
    }

    public function down()
    {
        $this->table('blocks')
            ->dropForeignKey('region_id')
            ->save();
    }
}
