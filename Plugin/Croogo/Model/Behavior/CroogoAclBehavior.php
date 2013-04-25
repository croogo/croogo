<?php

App::uses('AclBehavior', 'Model/Behavior');

/**
 * CroogoAcl Behavior
 *
 * PHP version 5
 *
 * @category Behavior
 * @package  Croogo.Croogo.Model.Behavior
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoAclBehavior extends AclBehavior {

/**
 * setup
 *
 * @param Model $model
 * @param array $config
 */
	public function setup(Model $model, $config = array()) {
		if (isset($config[0])) {
			$config['type'] = $config[0];
			unset($config[0]);
		}
		$this->settings[$model->name] = array_merge(array('type' => 'controlled'), $config);
		$this->settings[$model->name]['type'] = strtolower($this->settings[$model->name]['type']);

		$types = $this->_typeMaps[$this->settings[$model->name]['type']];

		if (!is_array($types)) {
			$types = array($types);
		}
		foreach ($types as $type) {
			$model->{$type} = ClassRegistry::init($type);
		}
	}

}
