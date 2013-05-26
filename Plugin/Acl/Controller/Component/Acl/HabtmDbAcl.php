<?php

App::uses('DbAcl', 'Controller/Component/Acl');

/**
 * HabtmDbAcl implements an ACL control system in the database like DbAcl with
 * User habtm Group checks
 *
 * @package Croogo.Acl.Controller.Component.Acl
 * @author Ceeram
 * @license MIT
 * @link http://github.com/ceeram/Authorize
 */
class HabtmDbAcl extends DbAcl {

	public $settings = array(
		'userModel' => 'Users.User',
		'groupAlias' => 'Role',
	);

/**
 * Initializes the containing component and sets the Aro/Aco objects to it.
 *
 * @param AclComponent $component
 * @return void
 */
	public function initialize(Component $component) {
		parent::initialize($component);
		if (!empty($component->settings['habtm'])) {
			$this->settings = array_merge($this->settings, $component->settings['habtm']);
		}
		$this->Acl = $component;
	}

/**
 * Checks if the given $aro has access to action $action in $aco
 * Check returns true once permissions are found, in following order:
 * User node
 * User::parentNode() node
 * Groupnodes of Groups that User has habtm links to
 *
 * @param string $aro ARO The requesting object identifier.
 * @param string $aco ACO The controlled object identifier.
 * @param string $action Action (defaults to *)
 * @return boolean Success (true if ARO has access to action in ACO, false otherwise)
 */
	public function check($aro, $aco, $action = "*") {
		if (parent::check($aro, $aco, $action)) {
			return true;
		}
		extract($this->settings);

		$User = ClassRegistry::init($userModel);
		list($plugin, $groupAlias) = pluginSplit($groupAlias);
		list($joinModel) = $User->joinModel($User->hasAndBelongsToMany[$groupAlias]['with']);
		$userField = $User->hasAndBelongsToMany[$groupAlias]['foreignKey'];
		$groupField = $User->hasAndBelongsToMany[$groupAlias]['associationForeignKey'];

		$node = $this->Acl->Aro->node($aro);
		$userId = Hash::extract($node, '0.Aro.foreign_key');
		$groupIDs = ClassRegistry::init($joinModel)->find('list', array(
			'fields' => array($groupField),
			'conditions' => array($userField => $userId),
			'recursive' => -1
		));
		foreach ((array)$groupIDs as $groupID) {
			$aro = array('model' => $groupAlias, 'foreign_key' => $groupID);
			$allowed = parent::check($aro, $aco, $action);
			if ($allowed) {
				return true;
			}
		}
		return false;
	}

}
