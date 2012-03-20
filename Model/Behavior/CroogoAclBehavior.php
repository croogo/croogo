<?php

App::uses('AclBehavior', 'Model/Behavior');

class CroogoAclBehavior extends AclBehavior {

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
