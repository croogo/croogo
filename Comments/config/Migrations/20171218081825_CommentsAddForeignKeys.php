<?php

use Migrations\AbstractMigration;

class CommentsAddForeignKeys extends AbstractMigration
{

    public function up()
    {
        $this->table('comments')
            ->addForeignKey('user_id', 'users', ['id'], [
                'constraint' => 'fk_comments2users',
                'delete' => 'RESTRICT',
            ]);
    }

    public function down()
    {
        $this->table('comments')
            ->dropForeignKey('user_id')
            ->save();
    }
}
