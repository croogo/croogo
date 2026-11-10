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
        $component = $this->loadComponent($name, $config);
        // Cake 5: loadComponent nie ustawia już property, a Controller nie ma
        // __isset() — przez co isset()/empty($this->X) w Croogo zawsze widziały
        // brak komponentu. Przywracamy zachowanie Cake 4.
        [, $prop] = pluginSplit($name);
        $this->{$prop} = $component;

        return $component;
    }
}
