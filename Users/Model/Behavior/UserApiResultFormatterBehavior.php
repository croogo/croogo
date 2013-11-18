<?php

App::uses('ModelBehavior', 'Model');

/**
 * User Api Result Formatter
 *
 * @package Croogo.Users.Model.Behavior
 * @since 1.6
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link http://www.croogo.org
 */
class UserApiResultFormatterBehavior extends ModelBehavior {

/**
 * afterFind
 */
	public function afterFind(Model $model, $results, $primary = true) {
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
