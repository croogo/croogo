<?php

namespace Croogo\Extensions\Controller;

interface HookableComponentInterface
{

	public function loadHookableComponent($name, array $config);
}
