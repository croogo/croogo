<?php

App::uses('UsersAppModel', 'Users.Model');

/**
 * RolesUser
 *
 *
 * @category Model
 * @package  Croogo.Users.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class RolesUser extends UsersAppModel {

	public $belongsTo = array(
		'User' => array(
			'className' => 'Users.User',
		),
		'Role' => array(
			'className' => 'Users.Role',
		),
	);

/**
 * Get Ids of Role's Aro assigned to user
 *
 * @param $userId integer user id
 * @return array array of Role Aro Ids
 */
	public function getRolesAro($userId) {
		$rolesUsers = $this->find('all', array(
			'fields' => 'role_id',
			'conditions' => array(
				'RolesUser.user_id' => $userId,
			),
			'cache' => array(
				'name' => 'user_roles_' . $userId,
				'config' => 'nodes_index',
			),
		));
		$aroIds = array();
		foreach ($rolesUsers as $rolesUser) {
			try {
				$aro = $this->Role->Aro->node(array(
					'model' => 'Role',
					'foreign_key' => $rolesUser['RolesUser']['role_id'],
				));
				$aroIds[] = $aro[0]['Aro']['id'];
			} catch (CakeException $e) {
				continue;
			}
		}
		return $aroIds;
	}
}
