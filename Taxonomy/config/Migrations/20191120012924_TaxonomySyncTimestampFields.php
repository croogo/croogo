<?php

use Cake\Database\Driver\Postgres;
use Cake\Database\Driver\Sqlite;
use Migrations\AbstractMigration;
use Phinx\Util\Literal;

class TaxonomySyncTimestampFields extends AbstractMigration
{

    public function up()
    {
        $adapter = $this->getAdapter();
        $connection = $adapter->getCakeConnection();
        $driver = $connection->getDriver();

        $timestampDefault = 'CURRENT_TIMESTAMP';
        if ($driver instanceof Postgres) {
            $timestampDefault = Literal::from('now()');
        }
        if ($driver instanceof Sqlite) {
            $timestampDefault = "(datetime(CURRENT_TIMESTAMP, 'utc'))";
        }

        $this->table('terms')
            ->renameColumn('updated', 'modified')
            ->renameColumn('updated_by', 'modified_by')
            ->update();

        $this->table('vocabularies')
            ->renameColumn('updated', 'modified')
            ->renameColumn('updated_by', 'modified_by')
            ->update();

        $this->table('taxonomies')
            ->addColumn('created', 'timestamp', [
                'null' => false,
                'default' => $timestampDefault,
            ])
            ->addColumn('modified', 'timestamp', [
                'null' => true,
            ])
           ->update();

        $this->table('types')
            ->renameColumn('updated', 'modified')
            ->renameColumn('updated_by', 'modified_by')
            ->update();

        $this->table('types_vocabularies')
            ->addColumn('created', 'timestamp', [
                'null' => false,
                'default' => $timestampDefault,
            ])
            ->addColumn('modified', 'timestamp', [
                'null' => true,
            ])
            ->update();

        $this->table('model_taxonomies')
            ->addColumn('created', 'timestamp', [
                'null' => false,
                'default' => $timestampDefault,
            ])
            ->addColumn('modified', 'timestamp', [
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {
        $this->table('terms')
            ->renameColumn('modified', 'update')
            ->renameColumn('modified_by', 'updated_by')
            ->update();

        $this->table('vocabularies')
            ->renameColumn('modified', 'update')
            ->renameColumn('modified_by', 'updated_by')
            ->update();

        $this->table('taxonomies')
            ->removeColumn('updated')
            ->removeColumn('modified')
            ->update();

        $this->table('types')
            ->renameColumn('updated', 'modified')
            ->renameColumn('updated_by', 'modified_by')
            ->update();

        $this->table('types_vocabularies')
            ->removeColumn('updated')
            ->removeColumn('modified')
            ->update();

        $this->table('model_taxonomies')
            ->removeColumn('updated')
            ->removeColumn('modified')
            ->update();
    }

}
