<?php

namespace Croogo\Core\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Croogo\Core\Croogo;

class AdminComponent extends ControllerPreparingComponent
{

	public function prepareController(Controller $controller)
	{
		$this->loadHelpers([
			'Croogo/Core.Croogo',
			'Croogo/Core.CroogoHtml',
			'Croogo/Core.CroogoPaginator',
			'Croogo/Core.Layout',
			'Croogo/Core.Custom',
			'Croogo/Core.CroogoForm',
			'Croogo/Core.Theme',
		]);

		if (!$controller->components()->has('Croogo/Core.CroogoBase')) {
			$this->loadComponent('Croogo/Core.CroogoBase');
		}
	}

}
