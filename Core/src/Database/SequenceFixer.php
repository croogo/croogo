<?php

namespace Croogo\Core\Database;

use Cake\Database\Driver\Postgres;
use Cake\Datasource\ConnectionManager;
use Cake\Log\LogTrait;
use Psr\Log\LogLevel;

class SequenceFixer {

    use LogTrait;

    public function fix($connectionName)
    {
        $db = ConnectionManager::get($connectionName);
        $driver = $db->getDriver();

        if ($driver instanceof Postgres) {
            $this->fixPostgres($db);
        }
    }

    protected function fixPostgres($db)
    {
        $config = $db->config();
        $database = $config['database'];
        $schema = isset($config['schema']) ? $config['schema'] : 'public';

        // gets a list of columns that uses a sequence as the default value
        $sql = sprintf("
            select table_name, column_name, column_default
              from information_schema.columns
             where table_catalog = '%s'
               and table_schema = '%s'
               and column_default like 'nextval%%'",
            $database, $schema
        );
        $columns = $db->query($sql)->fetchAll('assoc');

        // iterates columns and gets its current max value, increments it, and
        // alter it's starting value
        foreach ($columns as $column) {
            $nextValue = $db->query(
                sprintf('select MAX(%s.%s) as max from %s',
                    $column['table_name'],
                    $column['column_name'],
                    $column['table_name']
                )
            )->fetch('assoc');

            $nextValue = empty($nextValue['max']) ? 1 :  $nextValue['max'] + 1;

            preg_match_all("/'(.*)'/", $column['column_default'], $matches);
            $sequenceName = $matches[1][0];

            $sql = sprintf('alter sequence %s restart with %d',
                $sequenceName, $nextValue
            );
            $result = $db->execute($sql);
            $this->log(sprintf(
                    'Sequence %s reset to %d', $sequenceName, $nextValue
                ),
                LogLevel::WARNING
            );
        }
    }

}
