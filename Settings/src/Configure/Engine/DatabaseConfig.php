<?php
namespace Croogo\Settings\Configure\Engine;

use Cake\Core\Configure\ConfigEngineInterface;
use Cake\Log\Log;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class DatabaseConfig implements ConfigEngineInterface
{

    private $_table;

/**
 * @param Table $table
 */
    function __construct(Table $table = null)
    {
        if (!$table) {
            $table = TableRegistry::get('Croogo/Settings.Settings');
        }

        $this->_table = $table;
    }

/**
 * Read method is used for reading configuration information from sources.
 * These sources can either be static resources like files, or dynamic ones like
 * a database, or other datasource.
 *
 * @param string $key Key to read.
 * @return array An array of data to merge into the runtime configuration
 */
    public function read($key)
    {
        $settings = $this->_table->find('all');
        $config = [];

        foreach ($settings as $setting) {
            $config = Hash::insert($config, $setting->key, $setting->value);
        }

        return $config;
    }

/**
 * Dumps the configure data into source.
 *
 * @param string $key The identifier to write to.
 * @param array $data The data to dump.
 * @return bool True on success or false on failure.
 */
    public function dump($key, array $data)
    {
        Log::debug($key);
        Log::debug($data);

        return true;
    }
}
