<?php

App::uses('UsersAppModel', 'Users.Model');

/**
 * Role
 *
 * @category Model
 * @package  Croogo.Users.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Role extends UsersAppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	public $name = 'Role';

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
	);

/**
 * Validation
 *
 * @var array
 * @access public
 */
	public $validate = array();

/**
 * Display fields for this model
 *
 * @var array
 */
	protected $_displayFields = array(
		'id',
		'title',
		'alias',
	);

}
