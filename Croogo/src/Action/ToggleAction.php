<?php

namespace Croogo\Croogo\Action;

use Crud\Action\BaseAction;
use Crud\Traits\FindMethodTrait;
use Crud\Traits\RedirectTrait;
use Crud\Traits\SaveMethodTrait;
use Crud\Traits\ViewTrait;
use Crud\Traits\ViewVarTrait;

class ToggleAction extends BaseAction {

	/**
	 * Toggle Link status
	 *
	 * @param $id string Link id
	 * @param $status integer Current Link status
	 * @return void
	 */
	protected function _post($id = null, $status = null)
	{
		$this->_controller()->Croogo->fieldToggle($this->_table(), $id, $status);
	}

}
