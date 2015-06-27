<?php

namespace Croogo\Core\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;

abstract class ControllerPreparingComponent extends Component
{

	public function initialize(array $config)
	{
		parent::initialize($config);

		$this->prepareController($this->getController());
	}


	abstract public function prepareController(Controller $controller);

	/**
	 * @return Controller
	 */
	public function getController()
	{
		return $this->_registry->getController();
	}

	public function loadHelpers(array $helper)
	{
		$this->_registry->getController()->helpers += $helper;
	}

	/**
	 * Add a component to the controller's registry.
	 *
	 * This method will also set the component to a property.
	 * For example:
	 *
	 * `$this->loadComponent('Acl.Acl');`
	 *
	 * Will result in a `Toolbar` property being set.
	 *
	 * @param string $name The name of the component to load.
	 * @param array $config The config for the component.
	 * @return \Cake\Controller\Component
	 */
	public function loadComponent()
	{
		return call_user_func_array([$this->getController(), 'loadComponent'], func_get_args());
	}

}
