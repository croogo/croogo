<?php
/**
 * Install Shell
 *
 * PHP version 5
 *
 * @category Shell
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class InstallShell extends AppShell {

/**
 * Display help/options
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description(__('Install Utilities')
			)->addSubcommand('data', array(
				'help' => 'Generate data files',
				'parser' => array(
					'description' => 'Generate installation data files.',
					'arguments' => array(
						'table' => array(
							'required' => true,
							'help' => 'table name',
							),
						),
					),
				)
			);
		return $parser;
	}

/**
 * Prepares data in Config/Schema/data/ required for install plugin
 * You need to load the Install plugin temporarily to run this command.
 *
 * Usage: ./Console/cake install.install data table_name_here
 */
	public function data() {
		$connection = 'default';
		$table = trim($this->args['0']);
		$records = array();

		// get records
		$modelAlias = Inflector::camelize(Inflector::singularize($table));
		App::uses('Model', 'Model');
		$model = new Model(array('name' => $modelAlias, 'table' => $table, 'ds' => $connection));
		$records = $model->find('all', array(
			'recursive' => -1,
		));

		// generate file content
		$recordString = '';
		foreach ($records as $record) {
			$values = array();
			foreach ($record[$modelAlias] as $field => $value) {
				$values[] = "\t\t\t'$field' => '$value'";
			}
			$recordString .= "\t\tarray(\n";
			$recordString .= implode(",\n", $values);
			$recordString .= "\n\t\t),\n";
		}
		$content = "<?php\n";
			$content .= "class " . $modelAlias . "Data" . " {\n\n";
				$content .= "\tpublic \$table = '" . $table . "';\n\n";
				$content .= "\tpublic \$records = array(\n";
					$content .= $recordString;
				$content .= "\t);\n\n";
			$content .= "}\n";

		// write file
		$filePath = APP . 'Plugin' . DS . 'Install' . DS . 'Config' . DS . 'Data' . DS . $modelAlias . 'Data.php';
		if (!file_exists($filePath)) {
			touch($filePath);
		}
		App::uses('File', 'Utility');
		$file = new File($filePath, true);
		$file->write($content);

		$this->out('New file generated: ' . $filePath);
	}

}
