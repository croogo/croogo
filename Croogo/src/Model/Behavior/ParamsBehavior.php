<?php

namespace Croogo\Croogo\Model\Behavior;

use Cake\Datasource\ResultSetInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Croogo\Croogo\Utility\StringConverter;

/**
 * Params Behavior
 *
 * @category Behavior
 * @package  Croogo.Croogo.Model.Behavior
 * @since    1.3.1
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ParamsBehavior extends Behavior {

/**
 * @param Event $event
 * @param Query $query
 * @param $options
 */
	public function beforeFind(Event $event, Query $query, $options = []) {
		$query->formatResults(function (ResultSetInterface $results) {
			return $results->map(function ($row) {
				if (isset($row['params']) && !empty($row['params'])) {
					$row['params'] = $this->paramsToArray($row['params']);
				}
				return $row;
			});
		}, $query::PREPEND);
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
