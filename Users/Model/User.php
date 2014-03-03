<?php

App::uses('UsersAppModel', 'Users.Model');
App::uses('AuthComponent', 'Controller/Component');

/**
 * User
 *
 * @category Model
 * @package  Croogo.Users.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class User extends UsersAppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	public $name = 'User';

/**
 * Order
 *
 * @var string
 * @access public
 */
	public $order = 'User.name ASC';

/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
	public $actsAs = array(
		'Acl' => array(
			'className' => 'Croogo.CroogoAcl',
			'type' => 'requester',
		),
		'Croogo.Trackable',
		'Search.Searchable',
	);

/**
 * Model associations: belongsTo
 *
 * @var array
 * @access public
 */
	public $belongsTo = array('Users.Role');

/**
 * Validation
 *
 * @var array
 * @access public
 */
	public $validate = array();

/**
 * Filter search fields
 *
 * @var array
 * @access public
 */
	public $filterArgs = array(
		'name' => array('type' => 'like', 'field' => array('User.name', 'User.username')),
		'role_id' => array('type' => 'value'),
	);

/**
 * Display fields for this model
 *
 * @var array
 */
	protected $_displayFields = array(
		'id',
		'Role.title' => 'Role',
		'username',
		'name',
		'status' => array('type' => 'boolean'),
		'email',
	);

/**
 * Edit fields for this model
 *
 * @var array
 */
	protected $_editFields = array(
		'role_id',
		'username',
		'name',
		'email',
		'website',
		'status',
	);

/**
 * beforeDelete
 *
 * @param boolean $cascade
 * @return boolean
 */
	public function beforeDelete($cascade = true) {
		$this->Role->Behaviors->attach('Croogo.Aliasable');
		$adminRoleId = $this->Role->byAlias('admin');

		$current = AuthComponent::user();
		if (!empty($current['id']) && $current['id'] == $this->id) {
			return false;
		}
		if ($this->field('role_id') == $adminRoleId) {
			$count = $this->find('count', array(
				'conditions' => array(
					'User.id <>' => $this->id,
					'User.role_id' => $adminRoleId,
					'User.status' => true,
				)
			));
			return ($count > 0);
		}
		return true;
	}

/**
 * beforeSave
 *
 * @param array $options
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if (!empty($this->data['User']['password'])) {
			$this->data['User']['password'] = AuthComponent::password($this->data['User']['password']);
		}
		return true;
	}

}
