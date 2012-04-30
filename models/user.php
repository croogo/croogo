<?php
/**
 * User
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
class User extends AppModel {
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
	public $order = 'name ASC';

/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
	public $actsAs = array(
		'Acl' => array('type' => 'requester'),
	);

/**
 * Model associations: belongsTo
 *
 * @var array
 * @access public
 */
	public $belongsTo = array('Role');

/**
 * Validation
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'username' => array(
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'The username has already been taken.',
				'last' => true,
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This field cannot be left blank.',
				'last' => true,
			),
			'validAlias' => array(
				'rule' => '_validAlias',
				'message' => 'This field must be alphanumeric',
				'last' => true,
			),
		),
		'email' => array(
			'email' => array(
				'rule' => 'email',
				'message' => 'Please provide a valid email address.',
				'last' => true,
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Email address already in use.',
				'last' => true,
			),
		),
		'password' => array(
			'rule' => array('minLength', 6),
			'message' => 'Passwords must be at least 6 characters long.',
		),
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This field cannot be left blank.',
				'last' => true,
			),
			'validName' => array(
				'rule' => '_validName',
				'message' => 'This field must be alphanumeric',
				'last' => true,
			),
		),
		'website' => array(
			'url' => array(
				'rule' => 'url',
				'message' => 'This field must be a valid URL',
				'allowEmpty' => true,
			),
		),
	);

	public function parentNode() {
		if (!$this->id && empty($this->data)) {
			return null;
		}
		$data = $this->data;
		if (empty($this->data)) {
			$data = $this->read();
		}
		if (!isset($data['User']['role_id']) || !$data['User']['role_id']) {
			return null;
		} else {
			return array('Role' => array('id' => $data['User']['role_id']));
		}
	}

	public function afterSave($created) {
		if (!$created) {
			$parent = $this->parentNode();
			$parent = $this->node($parent);
			$node = $this->node();
			$aro = $node[0];
			$aro['Aro']['parent_id'] = $parent[0]['Aro']['id'];
			$this->Aro->save($aro);
		}
	}


}
