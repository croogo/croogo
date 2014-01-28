<?php

App::uses('AuthComponent', 'Controller/Component');
App::uses('CroogoStatus', 'Croogo.Lib');

/**
 * Publishable Behavior
 *
 * @category Croogo.Model.Behavior
 * @package  Croogo.Croogo.Model.Behavior
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 * @see      CroogoStatus
 */
class PublishableBehavior extends ModelBehavior {

/**
 * Setup
 *
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$this->_CroogoStatus = new CroogoStatus();
	}

/**
 * Get status for conditions in query based on current user's role id
 *
 * @return array Array of status
 */
	public function status(Model $model, $statusType = 'publishing', $accessType = 'public') {
		return $this->_CroogoStatus->status($statusType, $accessType);
	}

}
