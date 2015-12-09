<?php

namespace Croogo\Core\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;

/**
 * Base Api Component class
 *
 * @package Croogo.Croogo.Controller.Component
 * @since 1.6
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link http://www.croogo.org
 */
class BaseApiComponent extends Component
{

/**
 * Controller instance
 */
    protected $_controller;

/**
 * API Methods
 */
    protected $_apiMethods = [];

/**
 * API Version
 */
    protected $_apiVersion;

/**
 * Route prefix representing the API version
 */
    protected $_apiVersionPrefix;

/**
 * Initialize the component
 *
 * Setup properties and injects API methods to the controller
 * @return void
 */
    public function beforeFilter(Event $event)
    {
        $this->_controller = $event->subject();

        $this->_apiVersionPrefix = str_replace('.', '_', $this->_apiVersion);

        $methods = $this->_apiMethods;
        foreach ($methods as &$method) {
            $method = $this->_apiVersionPrefix . '_' . $method;
        }

        /**
         * @todo The Controller::$methods property has been removed. You should now use Controller::isAction() to determine whether or not a method name is an action. This change was made to allow easier customization of what is and is not counted as an action.
         */
//		$event->subject()->methods =
//			array_keys(array_flip($event->subject()->methods) +
//			array_flip($methods));
    }

/**
 * Get API version
 *
 * @return string API Version
 */
    public function version()
    {
        return $this->_apiVersion;
    }

/**
 * Verify that current request matches API version this component is serving
 *
 * @return bool
 */
    public function isVersionMatched()
    {
        if (!$this->_controller->request || !$this->_controller->request->is('api')) {
            return false;
        }
        $prefix = str_replace('.', '_', $this->_controller->request['prefix']);
        return $this->_apiVersionPrefix == $prefix;
    }

/**
 * Verify that $action exists in the current request
 *
 * @return bool
 */
    public function isValidAction($action)
    {
        return $this->isVersionMatched() && in_array($action, $this->_apiMethods);
    }

/**
 * Get a list of API methods
 *
 * @return array Array of method names
 */
    public function apiMethods()
    {
        return $this->_apiMethods;
    }
}
