<?php
/**
 * Role
 *
 * PHP version 5
 *
 * @category Model
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Role extends AppModel {
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
			'type' => 'requester',
		),
	);

/**
 * Validation
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'title' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Alias cannot be empty.',
				'last' => true,
			),
			'validName' => array(
				'rule' => '_validName',
				'message' => 'This field must be alphanumeric',
				'last' => true,
			),
		),
		'alias' => array(
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'This alias has already been taken.',
				'last' => true,
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Alias cannot be empty.',
				'last' => true,
			),
			'validAlias' => array(
				'rule' => '_validAlias',
				'message' => 'This field must be alphanumeric',
				'last' => true,
			),
		),
	);

	public function parentNode() {
		return null;
	}

}
