<?php

namespace Croogo\Core;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Request;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Croogo
 *
 * @package  Croogo.Croogo.Lib
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Croogo
{

    /**
     * Loads plugin's routes.php from app/config/routes.php.
     *
     * Plugin name is added to Hook.routes key of Configure object.
     *
     * @param string $pluginName plugin name
     * @deprecated Will be removed in the future.
     */
    public static function hookRoutes($pluginName)
    {
        $hooks = Configure::read('Hook.routes');
        if (!$hooks || !is_array($hooks)) {
            $hooks = [];
        }
        $hooks[] = $pluginName;
        Configure::write('Hook.routes', $hooks);
    }

    /**
     * Loads as a normal component from controller.
     *
     * @param string $controllerName Controller Name
     * @param mixed $componentName  Component name or array of Component and settings
     */
    public static function hookComponent($controllerName, $componentName)
    {
        if (is_string($componentName)) {
            $componentName = [$componentName];
        }
        self::hookControllerProperty($controllerName, '_appComponents', $componentName);
    }

    /**
     * Loads an API component to a controller during route setup.
     *
     * @param string $controllerName Controller Name
     * @param mixed $componentName  Component name or array of Component and settings
     */
    public static function hookApiComponent($controllerName, $componentName)
    {
        $defaults = [
            'priority' => 8,
        ];
        if (is_string($componentName)) {
            $component = [$componentName => $defaults];
        } else {
            $cName = key($componentName);
            $settings = Hash::merge($defaults, $componentName[$cName]);
            $component = [$cName => $settings];
        }

        self::hookControllerProperty($controllerName, '_apiComponents', $component);
    }

    /**
     * Attaches Behavior to a Table whenever loaded.
     *
     * @param string $tableName
     * @param string $behaviorName
     * @param array  $config
     */
    public static function hookBehavior($tableName, $behaviorName, $config = [])
    {
        self::hookTableProperty(
            App::className($tableName, 'Model/Table', 'Table'),
            'hookedBehaviors',
            [
                $behaviorName => $config
            ]
        );
    }

    /**
     * Loads as a normal helper via controller.
     *
     * @param string $controllerName
     * @param mixed $helperName Helper name or array of Helper and settings
     */
    public static function hookHelper($controllerName, $helperName)
    {
        if (is_string($helperName)) {
            $helperName = [$helperName];
        }
        self::hookViewBuilderOption($controllerName, 'helpers', $helperName);
    }

    /**
     * Shows plugin's admin_menu element in admin navigation under Extensions.
     *
     * @param string $pluginName
     */
    public static function hookAdminMenu($pluginName)
    {
        $pluginName = Inflector::underscore($pluginName);
        Configure::write('Admin.menus.' . $pluginName, 1);
    }

    /**
     * In admin panel for the provided action, the link will appear in table rows under 'Actions' column.
     *
     * @param string $action in the format ControllerName/action_name
     * @param string $title Link title
     * @param string $url
     */
    public static function hookAdminRowAction($action, $title, $url)
    {
        $action = base64_encode($action);
        $rowActions = Configure::read('Admin.rowActions');
        if (!is_array($rowActions)) {
            $rowActions = [];
        }
        if (!isset($rowActions[$action])) {
            $rowActions[$action] = [];
        }
        $rowActions[$action][$title] = $url;
        Configure::write('Admin.rowActions', $rowActions);
    }

    /**
     * Admin tab
     *
     * @param string $action  in the format ControllerName/action_name
     * @param string $title   Tab title
     * @param string $element element name, like plugin_name.element_name
     * @param array  $options array with options for the hook to take effect
     */
    public static function hookAdminTab($action, $title, $element, $options = [])
    {
        self::_hookAdminBlock('Admin.tabs', $action, $title, $element, $options);
    }

    /**
     * Admin box
     *
     * @param string $action  in the format ControllerName/action_name
     * @param string $title   Box title
     * @param string $element element name, like plugin_name.element_name
     * @param array  $options array with options for the hook to take effect
     */
    public static function hookAdminBox($action, $title, $element, $options = [])
    {
        self::_hookAdminBlock('Admin.boxes', $action, $title, $element, $options);
    }

    protected static function _hookAdminBlock($key, $action, $title, $element, $options = [])
    {
        $tabs = Configure::read($key);
        if (!is_array($tabs)) {
            $tabs = [];
        }
        if (!isset($tabs[$action])) {
            $tabs[$action] = [];
        }
        $tabs[$action][$title]['element'] = $element;
        $tabs[$action][$title]['options'] = $options;
        Configure::write($key, $tabs);
    }

    protected static function _getClassName($name, $type, $suffix)
    {
        if ($name !== '*') {
            return App::classname($name, $type, $suffix);
        }

        return '*';
    }

    /**
     * Hook table property
     *
     * Useful when tables need to be associated to another one, setting Behaviors, disabling cache, etc.
     *
     * @param string $tableName Table name (for e.g., Croogo/Nodes.Nodes)
     * @param string $property for e.g., actsAs
     * @param string|array $value
     */
    public static function hookTableProperty($tableName, $property, $value)
    {
        $configKeyPrefix = 'Hook.table_properties';

        $tableClass = self::_getClassName($tableName, 'Model/Table', 'Table');

        if ($tableClass) {
            self::_hookProperty($configKeyPrefix, $tableClass, $property, $value);
        }
    }

    /**
     * Hook controller property
     *
     * @param string $controllerName Controller name (for e.g., Croogo/Nodes.Nodes)
     * @param string $property for e.g., components
     * @param string|array $value
     */
    public static function hookControllerProperty($controllerName, $property, $value)
    {
        $configKeyPrefix = 'Hook.controller_properties';
        $controllerClass = self::_getClassName($controllerName, 'Controller', 'Controller');

        if ($controllerClass) {
            self::_hookProperty($configKeyPrefix, $controllerClass, $property, $value);
        }
    }

    /**
     * Hook controller property
     *
     * @param string $controllerName Controller name (for e.g., Nodes)
     * @param string $option for e.g., components
     * @param string|array $value
     */
    public static function hookViewBuilderOption($controllerName, $option, $value)
    {
        $configKeyPrefix = 'Hook.view_builder_options';
        $controllerClass = self::_getClassName($controllerName, 'Controller', 'Controller');

        if ($controllerClass) {
            self::_hookProperty($configKeyPrefix, $controllerClass, $option, $value);
        }
    }

    /**
     * Hook property
     *
     * @param string $configKeyPrefix
     * @param string $name
     * @param string $property
     * @param string $value
     */
    protected static function _hookProperty($configKeyPrefix, $name, $property, $value)
    {
        $propertyValue = Configure::read($configKeyPrefix . '.' . $name . '.' . $property);

        if (!is_array($propertyValue)) {
            $propertyValue = null;
        }

        if (is_array($value)) {
            if (is_array($propertyValue)) {
                $propertyValue = Hash::merge($propertyValue, $value);
            } else {
                $propertyValue = $value;
            }
        } else {
            $propertyValue = $value;
        }

        Configure::write($configKeyPrefix . '.' . $name . '.' . $property, $propertyValue);
    }

    public static function options($configKey, $object, $option = null)
    {
        if (is_string($object)) {
            $objectName = $object;
        } elseif ($object instanceof Request) {
            $pluginPath = $controller = null;
            $namespace = 'Controller';
            if (!empty($object->params['plugin'])) {
                $pluginPath = $object->params['plugin'] . '.';
            }
            if (!empty($object->params['controller'])) {
                $controller = $object->params['controller'];
            }
            if (!empty($object->params['prefix'])) {
                $prefixes = array_map(
                    'Cake\Utility\Inflector::camelize',
                    explode('/', $object->params['prefix'])
                );
                $namespace .= '/' . implode('/', $prefixes);
            }
            $objectName = App::className($pluginPath . $controller, $namespace, 'Controller');
        } elseif (is_object($object)) {
            $objectName = get_class($object);
        } else {
            return;
        }

        $options = Configure::read($configKey . '.' . $objectName);

        if (is_array(Configure::read($configKey . '.*'))) {
            $options = Hash::merge(Configure::read($configKey . '.*'), $options);
        }

        if ($option) {
            return $options[$option];
        }

        return $options;
    }

    /**
     * Applies properties set from hooks to an object in __construct()
     *
     * @param string $configKey
     */
    public static function applyHookProperties($configKey, $object = null)
    {
        if (empty($object)) {
            $object = self;
        }

        $hookProperties = self::options($configKey, $object);
        if (is_array($hookProperties)) {
            foreach ($hookProperties as $property => $value) {
                if (!$object->getProperty($property)) {
                    $object->setProperty($property, $value);
                } else {
                    $currentValues = $object->getProperty($property);
                    if (is_array($currentValues)) {
                        if (is_array($value)) {
                            $object->setProperty($property, Hash::merge($object->getProperty($property), $value));
                        } else {
                            $object->setProperty($property, $value);
                        }
                    } else {
                        $object->setProperty($property, $value);
                    }
                }
            }
        }
    }

    /**
     * Convenience method to dispatch event.
     *
     * Creates, dispatches, and returns a new Event object.
     *
     * @see Event::__construct()
     * @param string $name Name of the event
     * @param object $subject the object that this event applies to
     * @param mixed $data any value you wish to be transported with this event
     *
     * @return Event
     */
    public static function dispatchEvent($name, $subject = null, $data = null)
    {
        $event = new Event($name, $subject, $data);
        if ($subject) {
            $event = $subject->eventManager()->dispatch($event);
        } else {
            $event = EventManager::instance()->dispatch($event);
        }
        return $event;
    }

    /**
     * Get URL relative to the app
     *
     * @param array|string $url
     *
     * @return array
     */
    public static function getRelativePath($url = '/')
    {
        if (is_array($url)) {
            $absoluteUrl = Router::url($url, true);
        } else {
            $absoluteUrl = Router::url('/' . $url, true);
        }
        $path = '/' . str_replace(Router::url('/', true), '', $absoluteUrl);
        return $path;
    }

    /**
     * Merge Configuration
     *
     * @param string $key Configure key
     * @param array $config New configuration to merge
     * @param return array Array of merged configurations
     *
     * @return array|mixed
     */
    public static function mergeConfig($key, $config, $encode = false)
    {
        $values = Configure::read($key);
        if ($encode) {
            foreach ($config as $k => $v) {
                $tmp[base64_encode($k)] = $v;
            }
            $config = $tmp;
        }
        $values = Hash::merge((array)$values, $config);
        Configure::write($key, $values);
        return $values;
    }

    public static function translateModel($model, $config)
    {
        Croogo::mergeConfig('Translate.models', [
            $model => $config
        ], true);
    }

}
