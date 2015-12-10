<?php

namespace Croogo\Core\Controller;

interface HookableComponentInterface
{

    public function loadHookableComponent($name, array $config);
}
