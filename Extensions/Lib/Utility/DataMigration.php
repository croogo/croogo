<?php

App::uses('CakeLog', 'Log');
App::uses('ClassRegistry', 'Core');
App::uses('Model', 'Model');

/**
 * Data Migration Utility class
 *
 * @package Croogo.Extensions.Lib.Utility
 */
class DataMigration {

/**
 * Load data files
 *
 * @param string $path Path to directory containing data files
 * @param array $options Options array
 * @return bool True if loading was successful
 */
	public function load($path, $options = array()) {
		if (!is_dir($path)) {
			throw new CakeException('Argument not a directory: ' . $path);
		}
		$options = Hash::merge(array(
			'ds' => 'default',
		), $options);
		$dataObjects = App::objects('class', $path);
		foreach ($dataObjects as $data) {
			include ($path . DS . $data . '.php');
			$classVars = get_class_vars($data);
			$modelAlias = substr($data, 0, -4);
			$table = $classVars['table'];
			$records = $classVars['records'];
			$Model = new Model(array(
				'name' => $modelAlias,
				'table' => $table,
				'ds' => $options['ds'],
			));
			if (is_array($records) && count($records) > 0) {
				$i = 0;
				foreach ($records as $record) {
					$Model->create($record);
					$saved = $Model->save();
					if (!$saved) {
						CakeLog::error(sprintf(
							'Error loading row #%s for table `%s`',
							$i + 1,
							$table
						));
						return false;
					}
					$i++;
				}
				$Model->getDatasource()->resetSequence(
					$Model->useTable, $Model->primaryKey
				);
			}
			ClassRegistry::removeObject($modelAlias);
		}
		return true;
	}

}
