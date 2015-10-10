<?php

namespace Croogo\Extensions\Exception;

use Cake\Core\Exception\MissingPluginException;

class MissingThemeException extends MissingPluginException
{

    protected $_messageTemplate = 'Theme %s could not be found.';
}
