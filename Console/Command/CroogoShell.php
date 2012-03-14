<?php
/**
 * Croogo Shell
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
App::uses('Security', 'Utility');
class CroogoShell extends AppShell {
/**
 * Display help/options
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description(__d('croogo', 'Croogo Utilities')
			)->addSubcommand('password', array(
				'help' => 'Get hashed password',
				'parser' => array(
					'description' => 'Get hashed password',
					'arguments' => array(
						'password' => array(
							'required' => true,
							'help' => 'Password to hash',
							),
						),
					),
				)
			);
		return $parser;
	}

/**
 * Get hashed password
 *
 * Usage: ./Console/cake croogo password myPasswordHere
 */
	public function password() {
		$value = trim($this->args['0']);
		$this->out(Security::hash($value, null, true));
	}

/**
 * Prepares data in Config/Schema/data/ required for install plugin
 *
 * Usage: ./Console/cake croogo data table_name_here
 */
	public function data() {
		$connection = 'default';
		$table = trim($this->args['0']);
		$records = array();

		// get records
		$modelAlias = Inflector::camelize(Inflector::singularize($table));
		App::import('Model', 'Model', false);
		$model =& new Model(array('name' => $modelAlias, 'table' => $table, 'ds' => $connection));
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
		$className = $modelAlias . 'Data';
		$content = "<?php\n";
			$content .= "class " . $modelAlias . "Data" . " {\n\n";
				$content .= "\tpublic \$table = '" . $table . "';\n\n";
				$content .= "\tpublic \$records = array(\n";
					$content .= $recordString;
				$content .= "\t);\n\n";
			$content .= "}\n";

		// write file
		$filePath = APP . 'Config' . DS . 'Schema' . DS . 'data' . DS . Inflector::underscore($modelAlias) . '_data.php';
		if (!file_exists($filePath)) {
			touch($filePath);
		}
		App::uses('File', 'Utility');
		$file = new File($filePath, true);
		$file->write($content);

		$this->out('New file generated: ' . $filePath);
	}
	
}
