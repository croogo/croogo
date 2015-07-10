<?php

namespace Croogo\Contacts\Model\Table;

use Croogo\Core\Model\Table\CroogoTable;

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
class MessagesTable extends CroogoTable {

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

	public function initialize(array $config) {
		parent::initialize($config);
		$this->entityClass('Croogo/Contacts.Message');
		$this->belongsTo('Contacts', [
			'className' => 'Croogo/Contacts.Contacts',
			'foreignKey' => 'contact_id',
			'fields' => '',
			'order' => '',
			'counterCache' => true,
		]);


		$this->addBehavior('Croogo/Core.BulkProcess', [
			'actionsMap' => [
				'read' => 'bulkRead',
				'unread' => 'bulkUnread',
			],
		]);
		$this->addBehavior('Croogo/Core.Trackable');
		$this->addBehavior('Search.Searchable');
	}

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
