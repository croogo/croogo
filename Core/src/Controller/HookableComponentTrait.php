<?php

namespace Croogo\Core\Controller;

use Cake\Event\Event;

/**
 * Trait HookableComponentTrait
 * @package Croogo\Core\Controller
 */
trait HookableComponentTrait
{
    /**
     * @return void
     */
    protected function _dispatchBeforeInitialize()
    {
        $this->getEventManager()->dispatch(new Event('Controller.beforeInitialize', $this));
    }

    /**
     * @param $name
     * @param array $config
     *
     * @return mixed
     */
    public function _loadHookableComponent($name, array $config)
    {
        return $this->loadComponent($name, $config);
    }
}
