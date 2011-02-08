<?php
/**
 * AclGenerate Component
 *
 * PHP version 5
 *
 * @category Component
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclGenerateComponent extends Object {

/**
 * @param object $controller controller
 * @param array  $settings   settings
 */
    public function initialize(&$controller, $settings = array()) {
        $this->controller =& $controller;
        App::import('Core', 'File');
        $this->folder = new Folder;
    }

/**
 * List all controllers (including plugin controllers)
 *
 * @return array
 */
    public function listControllers() {
        $controllerPaths = array();

        // app/controllers
        $this->folder->path = APP.'controllers'.DS;
        $controllers = $this->folder->read();
        foreach ($controllers['1'] AS $c) {
            if (substr($c, strlen($c) - 4, 4) == '.php') {
                $cName = Inflector::camelize(str_replace('_controller.php', '', $c));
                $controllerPaths[$cName] = APP.'controllers'.DS.$c;
            }
        }

        // plugins/*/controllers/
        $this->folder->path = APP.'plugins'.DS;
        $plugins = $this->folder->read();
        foreach ($plugins['0'] AS $p) {
            if ($p != 'install') {
                $this->folder->path = APP.'plugins'.DS.$p.DS.'controllers'.DS;
                $pluginControllers = $this->folder->read();
                foreach ($pluginControllers['1'] AS $pc) {
                    if (substr($pc, strlen($pc) - 4, 4) == '.php') {
                        $pcName = Inflector::camelize(str_replace('_controller.php', '', $pc));
                        $controllerPaths[$pcName] = APP.'plugins'.DS.$p.DS.'controllers'.DS.$pc;
                    }
                }
            }
        }

        return $controllerPaths;
    }

/**
 * List actions of a particular Controller.
 *
 * @param string  $name Controller name (the name only, without having Controller at the end)
 * @param string  $path full path to the controller file including file extension
 * @param boolean $all  default is false. it true, private actions will be returned too.
 *
 * @return array
 */
    public function listActions($name, $path) {
        // base methods
        if (strpos($path, APP.'plugins') >= 0) {
            $plugin = $this->getPluginFromPath($path);
            $pacName = Inflector::camelize($plugin) . 'AppController'; // pac - PluginAppController
            $pacPath = APP.'plugins'.DS.$plugin.DS.$plugin.'_app_controller.php';
            App::import('Controller', $pacName, null, null, $pacPath);
            $baseMethods = get_class_methods($pacName);
        } else {
            $baseMethods = get_class_methods('AppController');
        }

        $controllerName = $name.'Controller';
        App::import('Controller', $controllerName, null, null, $path);
        $methods = get_class_methods($controllerName);

        // filter out methods
        foreach ($methods AS $k => $method) {
            if (strpos($method, '_', 0) === 0) {
                unset($methods[$k]);
                continue;
            }
            if (in_array($method, $baseMethods)) {
                unset($methods[$k]);
                continue;
            }
        }

        return $methods;
    }

/**
 * Get plugin name from path
 *
 * @param string $path file path
 *
 * @return string
 */
    public function getPluginFromPath($path) {
        $pathE = explode(DS, $path);
        $pluginsK = array_search('plugins', $pathE);
        $pluginNameK = $pluginsK + 1;
        $plugin = $pathE[$pluginNameK];

        return $plugin;
    }

}
?>