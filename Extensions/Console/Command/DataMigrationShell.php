<?php

namespace Croogo\Extensions\Console\Command;

use App\Console\Command\AppShell;
use Extensions\Lib\CroogoPlugin;
use Extensions\Lib\CroogoTheme;
use Extensions\Lib\ExtensionsInstaller;
use Extensions\Lib\Utility\DataMigration;
/**
 * DataMigration Shell
 *
 * @package  Croogo.Extensions.Console.Command
 * @since    1.6
 * @link     http://www.croogo.org
 */
class DataMigrationShell extends AppShell {

/**
 * Display help/options
 */
	public function getOptionParser() {
		return parent::getOptionParser()
			->description(__d('croogo', 'Data Migration Utility'))
			->addSubcommand('data', array(
				'help' => 'Generate data files',
				'parser' => array(
					'description' => 'Generate installation data files.',
					'options' => array(
						'plugin' => array(
							'help' => 'Plugin to generate data files into',
							'short' => 'p',
						),
						'connection' => array(
							'help' => 'Datasource to use when reading data',
							'short' => 'c',
						),
					),
					'arguments' => array(
						'table' => array(
							'required' => true,
							'help' => 'table name',
						),
					),
				),
			));
	}

/**
 * Prepares data in Config/Data/ required for install plugin
 *
 * Usage: ./Console/cake extensions.data_migration data table_name_here
 */
	public function data() {
		if (isset($this->params['plugin'])) {
			$plugin = $this->params['plugin'];
		}
		$connection = 'default';
		if (isset($this->params['connection'])) {
			$connection = $this->params['connection'];
		}
		$table = trim($this->args['0']);
		$name = Inflector::camelize(Inflector::singularize($table));
		$root = isset($plugin) ? Plugin::path($plugin) : APP;
		$output = $root . 'Config' . DS . 'Data' . DS . $name . 'Data.php';
		$records = array();

		$options = array(
			'model' => array(
				'name' => $name,
				'table' => $table,
				'connection' => $connection,
			),
			'output' => $output,
		);
		$DataMigration = new DataMigration();
		$success = $DataMigration->generate('all', array(
			'recursive' => -1,
		), $options);

		if ($success) {
			$this->out('<success>New file generated</success>: ' . str_replace(APP, '', $output));
		} else {
			$this->err('<error>Failed generating file for table</error>: ' . $table);
		}
	}

}