<?php

namespace Croogo\Core\Controller;

use Cake\Event\Event;

trait HookableComponentTrait
{

    public function _dispatchBeforeInitialize()
    {
        $this->eventManager()->dispatch(new Event('Controller.beforeInitialize', $this));
    }

    public function _loadHookableComponent($name, array $config)
    {
        return $this->loadComponent($name, $config);
    }
}
