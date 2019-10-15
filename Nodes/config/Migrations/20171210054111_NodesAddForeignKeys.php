<?php

use Migrations\AbstractMigration;

class NodesAddForeignKeys extends AbstractMigration
{

    public function up()
    {
        $this->table('nodes')
            ->addForeignKey('user_id', 'users', ['id'], [
                'constraint' => 'fk_nodes2users',
                'delete' => 'RESTRICT',
            ])
            ->save();
    }

    public function down()
    {
        $this->table('nodes')
            ->dropForeignKey('user_id')
            ->save();
    }
}
