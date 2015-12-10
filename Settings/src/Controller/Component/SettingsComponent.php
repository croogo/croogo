<?php

namespace Croogo\Settings\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;

/**
 * Settings Component
 *
 * @category Component
 * @package  Croogo.Settings.Controller.Component
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class SettingsComponent extends Component
{

/**
 * @var Controller
 */
    protected $_controller;

/**
 * startup
 */
    public function startup(Event $event)
    {
        $this->_controller = $event->subject();
        $this->_controller->loadModel('Croogo/Settings.Settings');
    }
}
