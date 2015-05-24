<?php

namespace Croogo\Nodes\Model\Behavior;

use App\Model\ModelBehavior;
/**
 * Node Api Result Formatter
 *
 * @package Croogo.Nodes.Model.Behavior
 * @since 2.0
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link http://www.croogo.org
 */
class NodeApiResultFormatterBehavior extends ModelBehavior {

/**
 * afterFind
 */
	public function afterFind(Model $model, $results, $primary = true) {
		$node = array();
		foreach ($results as $result) {
			$row = array();
			if (isset($result[$model->alias])) {
				$row = array_merge($row, $result[$model->alias]);
			}
			$keys = array_keys($result);
			foreach ($keys as $key) {
				if ($key == $model->alias) {
					continue;
				}
				$row[Inflector::variable($key)] = $result[$key];
			}
			$node[] = $row;
		};
		return $node;
	}

}
