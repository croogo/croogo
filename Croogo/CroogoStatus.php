<?php

namespace Croogo\Croogo;
App::uses('AuthComponent', 'Controller/Component');
App::uses('CakeLog', 'Log');
App::uses('Permission', 'Model');

/**
 * CroogoStatus
 *
 * @package  Croogo.Croogo.Lib
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoStatus implements ArrayAccess {

	const UNPUBLISHED = 0;

	const PUBLISHED = 1;

	const PREVIEW = 2;

	const PENDING = 0;

	const APPROVED = 1;

	const PROMOTED = 1;

	const UNPROMOTED = 0;

	protected $_statuses = array();

/**
 * Constructor
 */
	public function __construct() {
		$this->_statuses = array(
			'publishing' => array(
				self::UNPUBLISHED => __d('croogo', 'Unpublished'),
				self::PUBLISHED => __d('croogo', 'Published'),
				self::PREVIEW => __d('croogo', 'Preview'),
			),
			'approval' => array(
				self::APPROVED => __d('croogo', 'Approved'),
				self::PENDING => __d('croogo', 'Pending'),
			),
		);
		$event = Croogo::dispatchEvent('Croogo.Status.setup', null, $this);
	}

	public function offsetExists($offset) {
		return isset($this->_statuses[$offset]);
	}

	public function &offsetGet($offset) {
		$result = null;
		if (isset($this->_statuses[$offset])) {
			$result =& $this->_statuses[$offset];
		}
		return $result;
	}

	public function offsetSet($offset, $value) {
		$this->_statuses[$offset] = $value;
	}

	public function offsetUnset($offset) {
		if (isset($this->_statuses[$offset])) {
			unset($this->_statuses[$offset]);
		}
	}

/**
 * Returns a list of status id and its descriptions
 *
 * @return array List of status id and its descriptions
 */
	public function statuses($type = 'publishing') {
		if (array_key_exists($type, $this->_statuses)) {
			return $this->_statuses[$type];
		}
		return array();
	}

/**
 * Gets valid statuses based on type
 *
 * @param string $type Status type if applicable
 * @return array Array of statuses
 */
	public function status($statusType = 'publishing', $accessType = 'public') {
		$values = $this->_defaultStatus($statusType);
		$data = compact('statusType', 'accessType', 'values');
		$event = Croogo::dispatchEvent('Croogo.Status.status', null, $data);
		if (array_key_exists('values', $event->data)) {
			return $event->data['values'];
		} else {
			return $values;
		}
	}

/**
 * Default status
 */
	protected function _defaultStatus($statusType) {
		static $Permission = null;
		$status[$statusType] = array(self::PUBLISHED);
		$roleId = AuthComponent::user('role_id');
		$allow = false;

		if ($roleId && $roleId != 1) {
			if ($Permission === null) {
				$Permission = ClassRegistry::init('Permission');
			}
			try {
				$allow = $Permission->check(
					array('model' => 'Role', 'foreign_key' => $roleId),
					'controllers/Nodes/Nodes/admin_edit'
				);
			} catch (CakeException $e) {
				CakeLog::error($e->getMessage());
			}
		}

		switch ($statusType) {
			case 'publishing':
				if ($roleId == 1 || $allow) {
					$status[$statusType][] = self::PREVIEW;
				}
			break;
		}
		return $status[$statusType];
	}

/**
 * Get the status id from description
 *
 * @return int|mixed Status Id
 */
	public function byDescription($title, $statusType = 'publishing', $strict = true) {
		if (array_key_exists($statusType, $this->_statuses)) {
			return array_search($title, $this->_statuses[$statusType], $strict);
		}
		return false;
	}

/**
 * Get the description from id
 *
 * @return string|null Status Description
 */
	public function byId($id, $statusType = 'publishing') {
		if (isset($this->_statuses[$statusType][$id])) {
			return $this->_statuses[$statusType][$id];
		}
		return null;
	}

}
