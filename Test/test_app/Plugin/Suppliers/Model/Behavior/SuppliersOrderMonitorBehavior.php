<?php

class SuppliersOrderMonitorBehavior extends ModelBehavior {

	public function setup(Model $model, $config = array()) {
		$model->monitored = true;
	}

}
