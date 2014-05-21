<?php

namespace Croogo\Contacts\Model;

use Contacts\Model\ContactsAppModel;
/**
 * Message
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
		'Croogo.BulkProcess' => array(
			'actionsMap' => array(
				'read' => 'bulkRead',
				'unread' => 'bulkUnread',
			),
		),
		'Croogo.Trackable',
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
			'type' => 'value',
		),
		'status' => array(
			'type' => 'value',
		),
	);

/**
 * Mark messages as read in bulk
 *
 * @param array $ids Array of Message Ids
 * @return boolean True if successful, false otherwise
 */
	public function bulkRead($ids) {
		return $this->updateAll(
			array($this->escapeField('status') => 1),
			array($this->escapeField() => $ids)
		);
	}

/**
 * Mark messages as Unread in bulk
 *
 * @param array $ids Array of Message Ids
 * @return boolean True if successful, false otherwise
 */
	public function bulkUnread($ids) {
		return $this->updateAll(
			array($this->escapeField('status') => 0),
			array($this->escapeField() => $ids)
		);
	}

}
