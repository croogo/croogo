<?php

App::uses('ContactsAppModel', 'Contacts.Model');

/**
 * Message
 *
 * PHP version 5
 *
 * @category Model
 * @package  Croogo.Contacts.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Message extends ContactsAppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	public $name = 'Message';

/**
 * Behaviors
 *
 * @var array
 * @access public
 */
	public $actsAs = array(
		'Search.Searchable',
	);

/**
 * Validation
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'This field cannot be left blank.',
		),
		'email' => array(
			'rule' => 'email',
			'message' => 'Please provide a valid email address.',
		),
		'title' => array(
			'rule' => 'notEmpty',
			'message' => 'This field cannot be left blank.',
		),
		'body' => array(
			'rule' => 'notEmpty',
			'message' => 'This field cannot be left blank.',
		),
	);

/**
 * Model associations: belongsTo
 *
 * @var array
 * @access public
 */
	public $belongsTo = array(
		'Contact' => array(
			'className' => 'Contacts.Contact',
			'foreignKey' => 'contact_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'counterCache' => true,
		),
	);

/**
 * Filter fields
 *
 * @var array
 * @access public
 */
	public $filterArgs = array(
		'contact_id' => array(
			'type' => 'lookup',
			'model' => 'Contact',
			'modelField' => 'id',
			'formField' => 'contact_id',
		),
		'status' => array(
			'type' => 'value',
		),
	);

}
