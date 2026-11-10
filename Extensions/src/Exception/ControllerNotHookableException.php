<?php

namespace Croogo\Extensions\Exception;

use Croogo\Core\Exception\Exception;

class ControllerNotHookableException extends Exception
{

    protected string $_messageTemplate = 'Controller %s is not hookable, implement HookableComponentInterface';
}
