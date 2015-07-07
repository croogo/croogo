<?php

namespace Croogo\Core\Event;

use Cake\Cache\Cache;
use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\Log\Log;

/**
 * Croogo Event Manager class
 *
 * Descendant of EventManager, customized to map event listener objects
 *
 * @since 1.4
 * @package Croogo.Croogo.Event
 * @see EventManager
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoEventManager extends EventManager {

/**
 * A map of registered event listeners
 */
	protected $_listenersMap = array();

/**
 * Returns the globally available instance of a CroogoEventManager
 * @return CroogoEventManager the global event manager
 */
	public static function instance($manager = null) {
		if (empty(self::$_generalManager)) {
			return parent::instance(new CroogoEventManager());
		}
		return parent::instance($manager);
	}

/**
 * Load Event Handlers during bootstrap.
 *
 * Plugins can add their own custom EventHandler in Config/events.php
 * with the following format:
 *
 * $config = array(
 *     'EventHandlers' => array(
 *         'Example.ExampleEventHandler' => array(
 *             'eventKey' => null,
 *             'options' => array(
 *                 'priority' => 1,
 *                 'passParams' => false,
 *                 'className' => 'Plugin.ClassName',
 *      )));
 *
 * @return void
 */
	public static function loadListeners() {
		$eventManager = CroogoEventManager::instance();
		$cached = Cache::read('EventHandlers', 'cached_settings');
		if ($cached === false) {
			$eventHandlers = Configure::read('EventHandlers');
			$validKeys = array('eventKey' => null, 'options' => array());
			$cached = array();
			if (!empty($eventHandlers) && is_array($eventHandlers)) {
				foreach ($eventHandlers as $eventHandler => $eventOptions) {
					$eventKey = null;
					if (is_numeric($eventHandler)) {
						$eventHandler = $eventOptions;
						$eventOptions = array();
					}
					list($plugin, $class) = pluginSplit($eventHandler);
					if (!empty($eventOptions)) {
						extract(array_intersect_key($eventOptions, $validKeys));
					}
					if (isset($eventOptions['options']['className'])) {
						list($plugin, $class) = pluginSplit($eventOptions['options']['className']);
					}
					$class = App::className($eventHandler, 'Event');
					if (class_exists($class)) {
						$cached[] = compact('plugin', 'class', 'eventKey', 'eventOptions');
					} else {
						Log::error(__d('croogo', 'EventHandler %s not found in plugin %s', $class, $plugin));
					}
				}
				Cache::write('EventHandlers', $cached, 'cached_settings');
			}
		}
		foreach ($cached as $cache) {
			extract($cache);
			if (Plugin::loaded($plugin)) {
				$class = App::className($class, 'Event');
				$settings = isset($eventOptions['options']) ? $eventOptions['options'] : array();
				$listener = new $class($settings);
				$eventManager->attach($listener, $eventKey, $eventOptions);
			}
		}
	}

/**
 * Adds a new listener to an event.
 * @see EventManager::attach()
 * @return void
 */
	public function attach($callable, $eventKey = null, array $options = array()) {
		parent::on($callable, $eventKey, $options);
		if (is_object($callable)) {
			$key = get_class($callable);
			$this->_listenersMap[$key] = $callable;
		}
	}

/**
 * Removes a listener from the active listeners.
 * @see EventManager::detach()
 * @return void
 */
	public function detach($callable, $eventKey = null) {
		if (is_object($callable)) {
			$key = get_class($callable);
			unset($this->_listenersMap[$key]);
		}
		parent::off($callable, $eventKey);
	}

/**
 * Detach all listener objects belonging to a plugin
 * @param $plugin string
 * @return void
 */
	public function detachPluginSubscribers($plugin) {
		$eventHandlers = Configure::read('EventHandlers');
		if (empty($eventHandlers)) {
			return;
		}
		$eventHandlers = array_keys($eventHandlers);
		$eventHandlers = preg_grep('/^' . $plugin . '/', $eventHandlers);
		foreach ($eventHandlers as $eventHandler) {
			$className = App::className($eventHandler, 'Event');
			if (isset($this->_listenersMap[$className])) {
				$this->detach($this->_listenersMap[$className]);
			}
		}
	}

}
