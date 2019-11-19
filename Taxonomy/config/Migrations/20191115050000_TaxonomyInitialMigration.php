<?php

use Migrations\AbstractMigration;

class TaxonomyInitialMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('terms')
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('slug', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('params', 'text', [
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
            ->addColumn('updated_by', 'integer', [
                'default' => null,
                'limit' => 20,
                'null' => true,
            ])
            ->addIndex(
                [
                    'slug',
                ],
                [
                    'unique' => true,
                    'limit' => 190,
                ]
            )
            ->create();

        $this->table('vocabularies')
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('alias', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('required', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('multiple', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('tags', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('plugin', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
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
            ->addIndex(
                [
                    'alias',
                ],
                [
                    'unique' => true,
                    'limit' => 190,
                ]
            )
            ->create();

        $this->table('taxonomies')
            ->addColumn('parent_id', 'integer', [
                'default' => null,
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('term_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('vocabulary_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('lft', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('rght', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addTimestamps('created', 'modified')
            ->addForeignKey('term_id', 'terms', ['id'], [
                'constraint' => 'fk_taxonomies2terms',
                'delete' => 'RESTRICT',
            ])
            ->addForeignKey('vocabulary_id', 'vocabularies', ['id'], [
                'constraint' => 'fk_taxonomies2vocabularies',
                'delete' => 'RESTRICT',
            ])
            ->create();

        $this->table('types')
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('alias', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('format_show_author', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('format_show_date', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('format_use_wysiwyg', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('comment_status', 'integer', [
                'default' => 1,
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('comment_approve', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('comment_spam_protection', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('comment_captcha', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('params', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('plugin', 'string', [
                'default' => null,
                'limit' => 255,
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
            ->addIndex(
                [
                    'alias',
                ],
                [
                    'unique' => true,
                    'limit' => 190,
                ]
            )
            ->create();

        $this->table('types_vocabularies')
            ->addColumn('type_id', 'integer', [
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('vocabulary_id', 'integer', [
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('weight', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addTimestamps('created', 'modified')
            ->addIndex(
                [
                    'type_id', 'vocabulary_id',
                ],
                ['unique' => true]
            )
            ->create();

        $this->table('model_taxonomies')
            ->addColumn('model', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('taxonomy_id', 'integer', [
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addTimestamps('created', 'modified')
            ->addForeignKey('taxonomy_id', 'taxonomies', ['id'], [
                'constraint' => 'fk_model_taxonomies2taxonomies',
                'update' => 'CASCADE',
                'delete' => 'CASCADE',
            ])
            ->addIndex(
                [
                    'model', 'foreign_key', 'taxonomy_id',
                ],
                ['unique' => true]
            )
            ->create();
    }

    public function down()
    {
        $this->table('taxonomies')->drop()->save();
        $this->table('terms')->drop()->save();
        $this->table('types')->drop()->save();
        $this->table('vocabularies')->drop()->save();
        $this->table('types_vocabularies')->drop()->save();
        $this->table('model_taxonomies')->drop()->save();
    }
}
