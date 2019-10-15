<?php

namespace Croogo\Core\Controller;

/**
 * Interface HookableComponentInterface
 */
interface HookableComponentInterface
{

    /**
     * @param $name
     * @param array $config
     *
     * @return mixed
     */
    public function loadHookableComponent($name, array $config);
}
