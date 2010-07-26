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
 * Plugin name is added to Hook.bootstraps key of Configure object.
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
 * Loads hook component from CroogoComponent.
 *
 * Component name is added to Hook.components key of Configure object.
 *
 * @param string $componentName Component name
 */
    public function hookComponent($componentName) {
        $hooks = Configure::read('Hook.components');
        if (!$hooks || !is_array($hooks)) {
            $hooks = array();
        }
        $hooks[] = $componentName;
        Configure::write('Hook.components', $hooks);
    }
/**
 * Attaches Behavior to a Model whenever loaded.
 *
 * Information is stored in Hook.behaviors key of Configure object.
 *
 * @param string $modelName
 * @param string $behaviorName
 * @param array  $config
 */
    public function hookBehavior($modelName, $behaviorName, $config = array()) {
        $hooks = Configure::read('Hook.behaviors');
        if (!$hooks || !is_array($hooks)) {
            $hooks = array();
        }
        if (!isset($hooks[$modelName])) {
            $hooks[$modelName] = array(
                $behaviorName => $config,
            );
        } else {
            $hooks[$modelName][$behaviorName] = $config;
        }
        Configure::write('Hook.behaviors', $hooks);
    }
/**
 * Loads as a normal helper and calls all the extra callbacks supported in Croogo.
 *
 * Information is stored in Hook.helpers key of Configure object.
 *
 * @param string $helperName
 */
    public function hookHelper($helperName) {
        $hooks = Configure::read('Hook.helpers');
        if (!$hooks || !is_array($hooks)) {
            $hooks = array();
        }
        $hooks[] = $helperName;
        Configure::write('Hook.helpers', $hooks);
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
 */
    public function hookAdminTab($action, $title, $element) {
        $tabs = Configure::read('Admin.tabs');
        if (!is_array($tabs)) {
            $tabs = array();
        }
        if (!isset($tabs[$action])) {
            $tabs[$action] = array();
        }
        $tabs[$action][$title] = $element;
        Configure::write('Admin.tabs', $tabs);
    }
}
?>