<?php
/**
 * Croogo
 *
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Croogo {
/**
 * Loads plugin's routes.php from app/config/routes.php.
 *
 * Plugin name is added to Hook.routes key of Configure object.
 *
 * @param string $pluginName plugin name
 */
    public function hookRoutes($pluginName) {
        $pluginName = Inflector::underscore($pluginName);
        $hooks = Configure::read('Hook.routes');
        if (!$hooks || !is_array($hooks)) {
            $hooks = array();
        }
        $hooks[] = $pluginName;
        Configure::write('Hook.routes', $hooks);
    }
/**
 * Loads as a normal component from controller.
 *
 * @param string $controllerName Controller Name
 * @param string $componentName  Component name
 */
    public function hookComponent($controllerName, $componentName) {
        self::hookControllerProperty($controllerName, 'components', array($componentName));
    }
/**
 * Attaches Behavior to a Model whenever loaded.
 *
 * @param string $modelName
 * @param string $behaviorName
 * @param array  $config
 */
    public function hookBehavior($modelName, $behaviorName, $config = array()) {
        self::hookModelProperty($modelName, 'actsAs', array($behaviorName => $config));
    }
/**
 * Loads as a normal helper via controller.
 *
 * @param string $controllerName
 * @param string $helperName
 */
    public function hookHelper($controllerName, $helperName) {
        self::hookControllerProperty($controllerName, 'helpers', array($helperName));
    }
/**
 * Shows plugin's admin_menu element in admin navigation under Extensions.
 *
 * @param string $pluginName
 */
    public function hookAdminMenu($pluginName) {
        $pluginName = Inflector::underscore($pluginName);
        Configure::write('Admin.menus.'.$pluginName, 1);
    }
/**
 * In admin panel for the provided action, the link will appear in table rows under 'Actions' column.
 *
 * @param string $action in the format ControllerName/action_name
 * @param string $title Link title
 * @param string $url
 */
    public function hookAdminRowAction($action, $title, $url) {
        $rowActions = Configure::read('Admin.rowActions');
        if (!is_array($rowActions)) {
            $rowActions = array();
        }
        if (!isset($rowActions[$action])) {
            $rowActions[$action] = array();
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
    public function hookAdminTab($action, $title, $element, $options = array()) {
        $tabs = Configure::read('Admin.tabs');
        if (!is_array($tabs)) {
            $tabs = array();
        }
        if (!isset($tabs[$action])) {
            $tabs[$action] = array();
        }
        $tabs[$action][$title]['element'] = $element;
        $tabs[$action][$title]['options'] = $options;
        Configure::write('Admin.tabs', $tabs);
    }
/**
 * Hook model property
 *
 * Useful when models need to be associated to another one, setting Behaviors, disabling cache, etc.
 *
 * @param string $modelName Model name (for e.g., Node)
 * @param string $property  for e.g., actsAs
 * @param string $value     array or string
 */
    public function hookModelProperty($modelName, $property, $value) {
        $configKeyPrefix = 'Hook.model_properties';
        self::__hookProperty($configKeyPrefix, $modelName, $property, $value);
    }
/**
 * Hook controller property
 *
 * @param string $controllerName Controller name (for e.g., Nodes)
 * @param string $property       for e.g., components
 * @param string $value          array or string
 */
    public function hookControllerProperty($controllerName, $property, $value) {
        $configKeyPrefix = 'Hook.controller_properties';
        self::__hookProperty($configKeyPrefix, $controllerName, $property, $value);
    }
/**
 * Hook property
 *
 * @param string $configKeyPrefix
 * @param string $name
 * @param string $property
 * @param string $value
 */
    private function __hookProperty($configKeyPrefix, $name, $property, $value) {
        $propertyValue = Configure::read($configKeyPrefix . '.' . $name . '.' . $property);
        if (!is_array($propertyValue)) {
            $propertyValue = null;
        }
        if (is_array($value)) {
            if (is_array($propertyValue)) {
                $propertyValue = Set::merge($propertyValue, $value);
            } else {
                $propertyValue = $value;
            }
        } else {
            $propertyValue = $value;
        }
        Configure::write($configKeyPrefix . '.' . $name . '.' . $property, $propertyValue);
    }
/**
 * Applies properties set from hooks to an object in __construct()
 *
 * @param string $configKey
 */
    public function applyHookProperties($configKey) {
        $hookProperties = Configure::read($configKey . '.' . $this->name);
        if (is_array(Configure::read($configKey . '.*'))) {
            $hookProperties = Set::merge(Configure::read($configKey . '.*'), $hookProperties);
        }
        if (is_array($hookProperties)) {
            foreach ($hookProperties AS $property => $value) {
                if (!isset($this->$property)) {
                    $this->$property = $value;
                } else {
                    if (is_array($this->$property)) {
                        if (is_array($value)) {
                            $this->$property = Set::merge($this->$property, $value);
                        } else {
                            $this->$property = $value;
                        }
                    } else {
                        $this->$property = $value;
                    }
                }
            }
        }
    }
}
?>