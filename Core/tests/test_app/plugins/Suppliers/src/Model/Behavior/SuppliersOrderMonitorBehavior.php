<?php

namespace Suppliers\Model\Behavior;

use App\Model\ModelBehavior;
class SuppliersOrderMonitorBehavior extends ModelBehavior {

	public function setup(Model $model, $config = array()) {
		$model->monitored = true;
	}

}
