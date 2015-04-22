<?php

namespace Croogo\Croogo\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type;
use Croogo\Croogo\Utility\StringConverter;
use PDO;

class ParamsType extends Type {

	public function toPHP($value, Driver $driver)
	{
		return (empty($value)) ? '' : $this->paramsToArray($value);
	}

	public function marshal($value)
	{
		return;
	}

	public function toDatabase($value, Driver $driver)
	{
		return;
	}

	public function toStatement($value, Driver $driver)
	{
		if ($value === null) {
			return PDO::PARAM_NULL;
		}
		return PDO::PARAM_STR;
	}


/**
 * Converts a string of params to an array of formatted key/value pairs
 *
 * String is supposed to have one parameter per line in the format:
 * my_param_key=value_here
 * another_param=another_value
 *
 * @param Model $model
 * @param string $params
 * @return array
 */
	public function paramsToArray($params) {
		$converter = new StringConverter();
		$output = [];
		$params = preg_split('/[\r\n]+/', $params);
		foreach ($params as $param) {
			if (strlen($param) == 0) {
				continue;
			}

			if ($param[0] === '[') {
				$options = $converter->parseString('options', $param, [
					'convertOptionsToArray' => true,
				]);
				if (!empty($options)) {
					$output = array_merge($output, $options);
				}
				continue;
			}

			$paramE = explode('=', $param);
			if (count($paramE) == 2) {
				$key = $paramE['0'];
				$value = $paramE['1'];
				$output[$key] = trim($value);
			}
		}
		return $output;
	}
}
