<?php

App::uses('AuthComponent', 'Controller/Component');

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
        'Acl' => array(
            'className' => 'CroogoAcl',
            'type' => 'requester',
            ),
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
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'This field cannot be left blank.',
            ),
        ),
        'email' => array(
            'email' => array(
                'rule' => 'email',
                'message' => 'Please provide a valid email address.',
            ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Email address already in use.',
            ),
        ),
        'password' => array(
            'rule' => array('minLength', 6),
            'message' => 'Passwords must be at least 6 characters long.',
        ),
        'current_password' => array(
            'rule' => '_identical',
            ),
        'name' => array(
            'rule' => 'notEmpty',
            'message' => 'This field cannot be left blank.',
        ),
    );

    public function beforeSave($options = array()) {
        if (!empty($this->data['User']['password'])) {
            $this->data['User']['password'] = AuthComponent::password($this->data['User']['password']);
        }
        return true;
    }

    protected function _identical($check) {
        $currentPassword = $this->field('password');
        if ($currentPassword == AuthComponent::password($check['current_password'])) {
            return true;
        } else {
            return __('Current password did not match. Please, try again.');
        }
    }

}
?>