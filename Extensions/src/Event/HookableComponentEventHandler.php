<?php

namespace Croogo\Extensions\Event;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Croogo\Core\Croogo;
use Croogo\Extensions\Controller\HookableComponentInterface;

class HookableComponentEventHandler implements EventListenerInterface
{

	/**
	 * {@inheritDoc}
	 */
	public function implementedEvents()
	{
		return [
			'Controller.beforeInitialize' => 'initialize'
		];
	}

	public function initialize(Event $event)
	{
		/* @var \Cake\Controller\Controller|HookableComponentInterface $controller */
		$controller = $event->subject();

		if (!$controller instanceof HookableComponentInterface) {
			return;
		}

		$components = $this->_getComponents($controller);
		foreach ($components as $component => $config) {
			$controller->loadHookableComponent($component, $config);
		}
	}

	/**
	 * Setup the components array
	 *
	 * @param void
	 * @return void
	 */
	protected function _setupComponents() {
		$components = [];

		$components = Hash::merge(
			$this->_defaultComponents,
			$this->_appComponents
		);

		foreach ($components as $component => $config) {
			if (!is_array($config)) {
				$component = $config;
				$config = [];
			}

			$this->loadComponent($component, $config);
		}
	}

	public function loadComponent($name, array $config = []) {
		list(, $prop) = pluginSplit($name);
		list(, $modelProp) = pluginSplit($this->modelClass);
		$component = $this->components()->load($name, $config);
		if ($prop !== $modelProp) {
			$this->{$prop} = $component;
		}
		return $component;
	}

	private function _getComponents(Controller $controller)
	{
		$properties = Croogo::options('Hook.controller_properties', $controller);

		$components = [];
		foreach ($properties['_appComponents'] as $component => $config) {
			if (!is_array($config)) {
				$component = $config;
				$config = [];
			}

			$components[$component] = $config;
		}

		return $components;
	}
}
