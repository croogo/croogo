<?php

use Migrations\AbstractMigration;

class AssetsInitialMigration extends AbstractMigration {

    public $description = '';

    public function up()
    {
        $this->table('attachments')
            ->addColumn('title', 'string', [
                'null' => true, 'default' => null,
            ])
            ->addColumn('slug', 'string', [
                'null' => true, 'default' => null,
            ])
            ->addColumn('body', 'text', [
                'null' => true, 'default' => null,
            ])
            ->addColumn('excerpt', 'text', [
                'null' => true, 'default' => null,
            ])
            ->addColumn('status', 'boolean', [
                'null' => false, 'default' => false,
            ])
            ->addColumn('sticky', 'boolean', [
                'null' => false, 'default' => false,
            ])
            ->addColumn('visibility_roles', 'text', [
                'null' => true, 'default' => null,
            ])
            ->addColumn('hash', 'string', [
                'null' => true, 'default' => null, 'length' => 64,
            ])
            ->addColumn('plugin', 'string', [
                'null' => true, 'default' => null,
            ])
            ->addColumn('import_path', 'string', [
                'null' => true, 'default' => null, 'length' => 512,
            ])
            ->addColumn('asset_count', 'integer', [
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'null' => true,
            ])
            ->addColumn('created_by', 'integer', [
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => true,
            ])
            ->addColumn('modified_by', 'integer', [
                'null' => true,
            ])
            ->addIndex(['hash'], [
                'name' => 'ix_attachments_hash',
                'unique' => false,
            ])
            ->create();

        $this->table('assets')
            ->addColumn('parent_asset_id', 'integer', [
                'null' => true, 'default' => null,
            ])
            ->addColumn('foreign_key', 'integer', [
                'null' => true, 'default' => null,
            ])
            ->addColumn('model', 'string', [
                'null' => true, 'default' => null, 'length' => 64,
            ])
            ->addColumn('filename', 'string', [
                'null' => false, 'default' => null,
            ])
            ->addColumn('filesize', 'integer', [
                'null' => true, 'default' => null,
            ])
            ->addColumn('width', 'integer', [
                'null' => true, 'default' => null,
            ])
            ->addColumn('height', 'integer', [
                'null' => true, 'default' => null,
            ])
            ->addColumn('mime_type', 'string', [
                'null' => true, 'default' => null, 'length' => 32,
            ])
            ->addColumn('extension', 'string', [
                'null' => true, 'default' => null, 'length' => 5,
            ])
            ->addColumn('hash', 'string', [
                'null' => true, 'default' => null, 'length' => 64,
            ])
            ->addColumn('path', 'string', [
                'null' => false, 'default' => null,
            ])
            ->addColumn('adapter', 'string', [
                'null' => true, 'default' => null, 'length' => 32,
                'comment' => 'Gaufrette Storage Adapter Class',
            ])
            ->addColumn('created', 'datetime', [
                'null' => true, 'default' => null,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => true, 'default' => null,
            ])
            ->addIndex(['hash', 'path'], [
                'name' => 'ix_assets_hash',
            ])
            ->addIndex(['model', 'foreign_key'], [
                'name' => 'fk_assets',
            ])
            ->addIndex(['parent_asset_id', 'width', 'height'], [
                'name' => 'un_assets_dimension',
                'unique' => true,
            ])
            ->create();

        $this->table('asset_usages')
            ->addColumn('asset_id', 'integer', [
                'null' => false, 'default' => null,
            ])
            ->addColumn('model', 'string', [
                'null' => true, 'default' => null, 'length' => 64,
            ])
            ->addColumn('foreign_key', 'string', [
                'null' => true, 'default' => null, 'length' => 36,
            ])
            ->addColumn('type', 'string', [
                'length' => 20, 'null' => true, 'default' => null,
            ])
            ->addColumn('url', 'string', [
                'length' => 512, 'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'null' => true, 'default' => null,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => true, 'default' => null,
            ])
            ->addColumn('params', 'text', [
                'null' => true, 'default' => null,
            ])
            ->addIndex(['model', 'foreign_key'], [
                'name' => 'fk_asset_usage',
            ])
            ->create();
    }

    public function down()
    {
        $this->dropTable('asset_usages');
        $this->dropTable('assets');
        $this->dropTable('attachments');
    }

}
