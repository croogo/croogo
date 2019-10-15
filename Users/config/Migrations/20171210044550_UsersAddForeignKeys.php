<?php

use Migrations\AbstractMigration;

class UsersAddForeignKeys extends AbstractMigration
{

    public function up()
    {
        $this->table('users')
            ->addForeignKey('role_id', 'roles', ['id'], [
                'constraint' => 'fk_users2roles',
                'delete' => 'RESTRICT',
            ])
            ->save();

        $this->table('roles_users')
            ->addForeignKey('user_id', 'users', ['id'], [
                'constraint' => 'fk_roles_users2users',
                'delete' => 'RESTRICT',
            ])
            ->addForeignKey('role_id', 'roles', ['id'], [
                'constraint' => 'fk_roles_users2roles',
                'delete' => 'RESTRICT',
            ])
            ->save();
    }

    public function down()
    {
        $this->table('users')
            ->dropForeignKey('role_id')
            ->save();

        $this->table('roles_users')
            ->dropForeignKey('role_id')
            ->dropForeignKey('user_id')
            ->save();
    }
}
