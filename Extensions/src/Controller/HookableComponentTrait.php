<?php

namespace Croogo\Extensions\Controller;

use Cake\Event\Event;
use Croogo\Core\PropertyHookTrait;

trait HookableComponentTrait
{

	public function dispatchBeforeInitialize()
	{
		$this->eventManager()->dispatch(new Event('Controller.beforeInitialize', $this));
	}

	public function loadHookableComponent($name, array $config)
	{
		list(, $prop) = pluginSplit($name);
		list(, $modelProp) = pluginSplit($this->modelClass);

		$component = $this->components()->load($name, $config);
		if ($prop !== $modelProp) {
			$this->{$prop} = $component;
		}

		return $component;
	}
}
