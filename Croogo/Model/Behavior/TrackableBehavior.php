<?php

App::uses('ModelBehavior', 'Model');
App::uses('AuthComponent', 'Controller/Component');
App::uses('CakeSession', 'Model/Datasource');

/**
 * Trackable Behavior
 *
 * Populate `created_by` and `updated_by` fields from session data.
 *
 * @package  Croogo.Croogo.Model.Behavior
 * @since    1.6
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TrackableBehavior extends ModelBehavior {

/**
 * Default settings
 */
	protected $_defaults = array(
		'userModel' => 'Users.User',
		'fields' => array(
			'created_by' => 'created_by',
			'updated_by' => 'updated_by',
			),
		);

/**
 * Setup
 */
	public function setup(Model $model, $config = array()) {
		$this->settings[$model->alias] = Set::merge($this->_defaults, $config);
		if ($this->_hasTrackableFields($model)) {
			$this->_setupBelongsTo($model);
		}
	}

/**
 * Checks wether model has the required fields
 *
 * @return bool True if $model has the required fields
 */
	protected function _hasTrackableFields(Model $model) {
		$fields = $this->settings[$model->alias]['fields'];
		return
			$model->hasField($fields['created_by']) &&
			$model->hasField($fields['updated_by']);
	}

/**
 * Bind relationship on the fly
 */
	protected function _setupBelongsTo(Model $model) {
		if (!empty($model->belongsTo['TrackableCreator'])) {
			return;
		}
		$config = $this->settings[$model->alias];
		list($plugin, $modelName) = pluginSplit($config['userModel']);
		$className = isset($plugin) ? $plugin . '.' . $modelName : $modelName;
		$model->bindModel(array(
			'belongsTo' => array(
				'TrackableCreator' => array(
					'className' => $className,
					'foreignKey' => $config['fields']['created_by'],
				),
				'TrackableUpdater' => array(
					'className' => $className,
					'foreignKey' => $config['fields']['updated_by'],
				),
			)
		), false);
	}

/**
 * Fill the created_by and updated_by fields
 *
 * Note: Since shells do not have Sessions, created_by/updated_by fields
 * will not be populated. If a shell needs to populate these fields, you
 * can simulate a logged in user by setting `Trackable.Auth` config:
 *
 *   Configure::write('Trackable.User', array('id' => 1));
 *
 * Note that value stored in this variable overrides session data.
 */
	public function beforeSave(Model $model, $options = array()) {
		if (!$this->_hasTrackableFields($model)) {
			return true;
		}
		$config = $this->settings[$model->alias];

		$User = ClassRegistry::init($config['userModel']);
		$userAlias = $User->alias;
		$userPk = $User->primaryKey;

		$user = Configure::read('Trackable.Auth.User');
		if (!$user && CakeSession::started()) {
			$user = AuthComponent::user();
		}

		if ($user && array_key_exists($userPk, $user)) {
			$userId = $user[$userPk];
		}

		if (empty($user) || empty($userId)) {
			return true;
		}

		$alias = $model->alias;
		$createdByField = $config['fields']['created_by'];
		$updatedByField = $config['fields']['updated_by'];

		if (empty($model->data[$alias][$createdByField])) {
			if (!$model->exists()) {
				$model->data[$alias][$createdByField] = $user[$userPk];
			}
		}
		$model->data[$alias][$updatedByField] = $userId;

		if (!empty($model->whitelist)) {
			$model->whitelist[] = $createdByField;
			$model->whitelist[] = $updatedByField;
		}

		return true;
	}

}
