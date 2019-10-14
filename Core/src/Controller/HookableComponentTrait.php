<?php

namespace Croogo\Core\Controller;

use Cake\Event\Event;

trait HookableComponentTrait
{
    protected function _dispatchBeforeInitialize()
    {
        $this->getEventManager()->dispatch(new Event('Controller.beforeInitialize', $this));
    }

    public function loadHookableComponent($name, array $config)
    {
        return $this->loadComponent($name, $config);
    }
}
