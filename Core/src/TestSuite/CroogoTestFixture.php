<?php

namespace Croogo\Core\TestSuite;

use Cake\Database\Schema\TableSchema;
use Cake\Datasource\ConnectionInterface;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * CroogoTestFixture class
 *
 * Cake 5: TestFixture nie tworzy już tabel z `$fields` (schema-from-reflection).
 * Fixtury Croogo są legacy (definiują schemat inline w `$fields`) — ta klasa
 * odtwarza stare zachowanie: przy inicjalizacji tworzy tabelę na połączeniu
 * testowym, jeśli jeszcze nie istnieje.
 *
 * @category TestSuite
 * @package  Croogo
 * @version  1.4
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoTestFixture extends TestFixture
{
    /**
     * Legacy definicja schematu (format Cake 3: kolumny + _constraints/_indexes/_options)
     *
     * @var array
     */
    public $fields = [];

    public function init(): void
    {
        if (!empty($this->fields)) {
            $this->create(ConnectionManager::get($this->connection()));
        }

        parent::init();
    }

    /**
     * Tworzy tabelę z legacy `$fields` (no-op gdy tabela istnieje).
     *
     * BC: wołane też wprost z tests/bootstrap.php.
     */
    public function create(ConnectionInterface $connection): bool
    {
        if (empty($this->fields)) {
            return false;
        }
        if (empty($this->table)) {
            $this->table = $this->_tableFromClass();
        }
        if (in_array($this->table, $connection->getSchemaCollection()->listTables(), true)) {
            return true;
        }

        $schema = new TableSchema($this->table);
        foreach ($this->fields as $field => $data) {
            if ($field === '_constraints') {
                foreach ($data as $name => $constraint) {
                    if (is_string($constraint['columns'] ?? null)) {
                        $constraint['columns'] = [$constraint['columns']];
                    }
                    $schema->addConstraint($name, $constraint);
                }
            } elseif ($field === '_indexes') {
                foreach ($data as $name => $index) {
                    $schema->addIndex($name, $index);
                }
            } elseif ($field === '_options') {
                $schema->setOptions($data);
            } else {
                $schema->addColumn($field, $data);
            }
        }

        foreach ($schema->createSql($connection) as $sql) {
            $connection->execute($sql);
        }

        return true;
    }

    /**
     * Nazwa tabeli z nazwy klasy fixtury (legacy pomocnik dla create()).
     */
    protected function _tableFromClass(): string
    {
        [, $class] = \Cake\Core\namespaceSplit(static::class);
        preg_match('/^(.*)Fixture$/', $class, $matches);
        $table = $matches[1] ?? $class;

        return \Cake\Utility\Inflector::tableize($table);
    }
}
