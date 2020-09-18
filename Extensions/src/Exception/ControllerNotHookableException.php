<?php
declare(strict_types=1);

namespace Croogo\Extensions\Exception;

use Croogo\Core\Exception\Exception;

class ControllerNotHookableException extends Exception
{

    protected $_messageTemplate = 'Controller %s is not hookable, implement HookableComponentInterface';
}
