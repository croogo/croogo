<?php

use Migrations\AbstractMigration;

class UsersInitialMigration extends AbstractMigration
{
    public function up()
    {

        $this->table('roles')
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
            ->addIndex(
                [
                    'alias',
                ],
                ['unique' => true]
            )
            ->create();

        $this->table('users')
            ->addColumn('role_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('username', 'string', [
                'default' => null,
                'limit' => 60,
                'null' => false,
            ])
            ->addColumn('password', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('email', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('website', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('activation_key', 'string', [
                'default' => null,
                'limit' => 60,
                'null' => true,
            ])
            ->addColumn('image', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('bio', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('status', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('timezone', 'string', [
                'default' => 'UTC',
                'limit' => 40,
                'null' => false,
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
            ->addForeignKey('role_id', 'roles', ['id'], [
                'constraint' => 'fk_users2roles',
                'delete' => 'RESTRICT',
            ])
            ->create();

        $this->table('roles_users')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('role_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('granted_by', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addTimestamps('created', 'updated')
            ->addForeignKey('user_id', 'users', ['id'], [
                'constraint' => 'fk_roles_users2users',
                'delete' => 'RESTRICT',
            ])
            ->addForeignKey('role_id', 'roles', ['id'], [
                'constraint' => 'fk_roles_users2roles',
                'delete' => 'RESTRICT',
            ])
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();
    }

    public function down()
    {
        $this->table('roles')->drop()->save();
        $this->table('users')->drop()->save();
        $this->table('roles_users')->drop()->save();
    }
}
