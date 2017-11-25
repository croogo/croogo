<?php

namespace Croogo\Core\Controller;

interface HookableComponentInterface
{

    public function _loadHookableComponent($name, array $config);
}
