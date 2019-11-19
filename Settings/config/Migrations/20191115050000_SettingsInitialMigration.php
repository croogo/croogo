<?php

use Migrations\AbstractMigration;

class SettingsInitialMigration extends AbstractMigration
{
    public function up()
    {

        $this->table('languages')
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('native', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('alias', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('locale', 'string', [
                'default' => null,
                'limit' => 15,
                'null' => false,
            ])
            ->addColumn('status', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('weight', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addTimestamps('created', 'modified')
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
            ->addIndex(['locale'], [
                'name' => 'ix_languages_locale',
                'unique' => true,
            ])
            ->create();

        $this->table('settings')
            ->addColumn('key', 'string', [
                'default' => null,
                'limit' => 64,
                'null' => false,
            ])
            ->addColumn('value', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('input_type', 'string', [
                'default' => 'text',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('editable', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('weight', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('params', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addTimestamps('created', 'modified')
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
                    'key',
                ],
                ['unique' => true]
            )
            ->create();
    }

    public function down()
    {
        $this->table('languages')->drop()->save();
        $this->table('settings')->drop()->save();
    }
}
