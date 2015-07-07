<?php

namespace Croogo\Core\Model\Behavior;

use Cake\ORM\Behavior;
use Croogo\Core\Status;

/**
 * Publishable Behavior
 *
 * Provides status and period filtering. Requires the following fields:
 *   - `status` integer value from `Status::statuses()
 *   - `publish_start` datetime indicates the start of publishing period
 *   - `publish_end` datetime indicates the end of publishing period
 *
 * @category Croogo.Model.Behavior
 * @package  Croogo.Croogo.Model.Behavior
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 * @see      CroogoStatus
 */
class PublishableBehavior extends Behavior {

	protected $_defaultConfig = [
		'admin' => false,
		'fields' => [
			'publish_start' => 'publish_start',
			'publish_end' => 'publish_end',
		],
	];

/**
* Setup
*
* Valid options:
*
*   `admin`: Enable/disable date filtering for users with Admin roles
*   `fields`: Specifies the physical field name to use
 *
 * @return void
 */
	public function initialize(array $config) {
		parent::initialize($config);

		$this->_CroogoStatus = new Status();
	}


	/**
 * Get status for conditions in query based on current user's role id
 *
 * @return array Array of status
 */
	public function status($statusType = 'publishing', $accessType = 'public') {
		return $this->_CroogoStatus->status($statusType, $accessType);
	}

/**
 * Filter records based on period
 *
 * @return array Options passed to Model::find()
 */
//	public function beforeFind(Model $model, $query = array()) {
//		$settings = $this->settings[$model->alias];
//		if (!$model->Behaviors->enabled('Publishable')) {
//			return $query;
//		}
//
//		if ($settings['admin'] === false) {
//			if (AuthComponent::user('role_id') == 1) {
//				return $query;
//			}
//		}
//
//		if (!$model->hasField($settings['fields']['publish_start']) ||
//			!$model->hasField($settings['fields']['publish_end'])
//		) {
//			return $query;
//		}
//
//		$date = isset($query['date']) ? $query['date'] : date('Y-m-d H:i:s');
//		$start = $model->escapeField($settings['fields']['publish_start']);
//		$end = $model->escapeField($settings['fields']['publish_end']);
//
//		if (is_string($query['conditions'])) {
//			$query['conditions'] = (array)$query['conditions'];
//		}
//
//		$query['conditions'][] = array(
//			'OR' => array(
//				$start => null,
//				array(
//					$start . ' <> ' => null,
//					$start . ' <=' => $date,
//				),
//			),
//		);
//
//		$query['conditions'][] = array(
//			'OR' => array(
//				$end => null,
//				array(
//					$end . ' <> ' => null,
//					$end . ' >=' => $date,
//				),
//			),
//		);
//
//		return $query;
//	}

}
