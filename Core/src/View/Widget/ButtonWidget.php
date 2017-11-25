<?php

namespace Croogo\Core\View\Widget;

use BootstrapUI\View\Helper\OptionsAwareTrait;
use Cake\View\Form\ContextInterface;

class ButtonWidget extends \BootstrapUI\View\Widget\ButtonWidget
{
    /**
     * A list of allowed styles for buttons.
     *
     * @var array
     */
    public $buttonClasses = [
        'default',
        'btn-default',
        'success',
        'btn-success',
        'warning',
        'btn-warning',
        'danger',
        'btn-danger',
        'info',
        'btn-info',
        'primary',
        'btn-primary',
        'btn-outline-success',
        'btn-outline-primary',
        'btn-outline-warning',
        'btn-outline-danger',
        'btn-outline-info',
    ];
}
