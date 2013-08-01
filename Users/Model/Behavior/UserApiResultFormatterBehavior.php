<?php

App::uses('ModelBehavior', 'Model');

/**
 * User Api Result Formatter
 */
class UserApiResultFormatterBehavior extends ModelBehavior {

/**
 * afterFind
 */
	public function afterFind(Model $model, $results, $primary) {
		$user = array();
		foreach ($results as $result) {
			$row = array();
			if (isset($result['User'])) {
				$row = array_merge($row, $result['User']);
			};
			if (isset($result['Role'])) {
				$row['role'] = $result['Role'];
			};
			$user[] = $row;
		};
		return $user;
	}

}
