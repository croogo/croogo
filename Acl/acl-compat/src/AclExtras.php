<?php

/**
 * Acl Extras.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2008-2013, Mark Story.
 * @link https://mark-story.com
 * @author Mark Story <mark@mark-story.com>
 * @license https://www.opensource.org/licenses/mit-license.php The MIT License
 */

namespace Acl;

use Acl\Controller\Component\AclComponent;
use Cake\Console\ConsoleIo;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Croogo\Core\Utility\FsUtils;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use ReflectionException;

/**
 * Provides features for additional ACL operations.
 * Can be used in either a CLI or Web context.
 */
class AclExtras
{

    /**
     * Contains instance of AclComponent
     *
     * @var \Acl\Controller\Component\AclComponent
     */
    public $Acl;

    /**
     * Acos table (PHP 8.2+: deklaracja zamiast dynamic property)
     *
     * @var \Croogo\Acl\Model\Table\AcosTable
     */
    public $Aco;

    /**
     * Contains arguments parsed from the command line.
     *
     * @var array
     */
    public $args;

    /**
     * Contains database source to use
     *
     * @var string
     */
    public $dataSource = 'default';

    /**
     * Root node name.
     *
     * @var string
     */
    public $rootNode = 'controllers';

    /**
     * Internal Clean Actions switch
     *
     * @var bool
     */
    protected $_clean = false;

    /**
     * Contains app route prefixes
     *
     * @var array
     */
    protected $prefixes = [];

    /**
     * Contains plugins route prefixes
     *
     * @var array
     */
    protected $pluginPrefixes = [];

    /**
     * List of ACOs found during synchronization
     *
     * @var array
     */
    protected $foundACOs = [];

    /** @var \Cake\Controller\Controller */
    protected $controller;

    /** @var \Cake\Console\ConsoleIo */
    protected $Shell;

    /**
     * Start up And load Acl Component / Aco model
     *
     * @param \Cake\Controller\Controller $controller Controller instance
     * @return void
     */
    public function startup($controller = null)
    {
        if (!$controller) {
            $controller = new Controller(new ServerRequest());
        }
        $registry = new ComponentRegistry();
        $this->Acl = new AclComponent($registry, Configure::read('Acl'));
        $this->Aco = $this->Acl->Aco;
        $this->controller = $controller;
        $this->_buildPrefixes();
    }

    /**
     * Output a message.
     *
     * Will either use shell->out, or controller->Flash->success()
     *
     * @param string $msg The message to output.
     * @return void
     */
    public function out($msg)
    {
        if (!empty($this->controller->Flash)) {
            $this->controller->Flash->success($msg);
        } else {
            $this->Shell->out($msg);
        }
    }

    /**
     * Output an error message.
     *
     * Will either use shell->err, or controller->Flash->error()
     *
     * @param string $msg The message to output.
     * @return void
     */
    public function err($msg)
    {
        if (!empty($this->controller->Flash)) {
            $this->controller->Flash->error($msg);
        } else {
            $this->Shell->err($msg);
        }
    }

    /**
     * Sync the ACO table
     *
     * @param array $params An array of parameters
     * @return void
     */
    public function acoSync($params = [])
    {
        $this->_clean = true;
        $this->acoUpdate($params);
    }

    /**
     * Updates the Aco Tree with new controller actions.
     *
     * @param array $params An array of parameters
     * @return bool
     */
    public function acoUpdate($params = [])
    {
        $root = $this->_checkNode($this->rootNode, $this->rootNode, null);
        if (empty($params['plugin'])) {
            $plugins = Plugin::loaded();
            $this->_processControllers($root);
            $this->_processPrefixes($root);
            $this->_processPlugins($root, $plugins);
        } else {
            $plugin = $params['plugin'];
            if (!Plugin::loaded($plugin)) {
                $this->err(__d('cake_acl', "<error>Plugin {0} not found or not activated.</error>", [$plugin]));

                return false;
            }
            $plugins = [$params['plugin']];
            $this->_processPlugins($root, $plugins);
            $this->foundACOs = array_slice($this->foundACOs, 1, null, true);
        }

        if ($this->_clean) {
            foreach ($this->foundACOs as $parentId => $acosList) {
                $this->_cleaner($parentId, $acosList);
            }
        }
        $this->out(__d('cake_acl', '<success>Aco Update Complete</success>'));

        return true;
    }

    /**
     * Updates the Aco Tree with all App controllers.
     *
     * @param \Acl\Model\Entity\Aco $root The root node of Aco Tree
     * @return void
     */
    protected function _processControllers($root)
    {
        $controllers = $this->getControllerList();
        $this->foundACOs[$root->id] = $this->_updateControllers($root, $controllers);
    }

    /**
     * Updates the Aco Tree with all App route prefixes.
     *
     * @param \Acl\Model\Entity\Aco $root The root node of Aco Tree
     * @return void
     */
    protected function _processPrefixes($root)
    {
        foreach (array_keys($this->getPrefixes()) as $prefix) {
            $controllers = $this->getControllerList(null, $prefix);
            $path = $this->rootNode . '/' . $prefix;
            $pathNode = $this->_checkNode($path, $prefix, $root->id);
            $this->foundACOs[$root->id][] = $prefix;
            if (isset($this->foundACOs[$pathNode->id])) {
                $this->foundACOs[$pathNode->id] += $this->_updateControllers($pathNode, $controllers, '', $prefix);
            } else {
                $this->foundACOs[$pathNode->id] = $this->_updateControllers($pathNode, $controllers, '', $prefix);
            }
        }
    }

    /**
     * Returns the aliased name for the plugin (Needed in order to correctly handle nested plugins)
     *
     * @param string $plugin The name of the plugin to alias
     * @return string
     */
    protected function _pluginAlias(string $plugin) :string
    {
        return preg_replace('/\//', '\\', Inflector::camelize($plugin));
    }

    /**
     * Updates the Aco Tree with all Plugins.
     *
     * @param \Acl\Model\Entity\Aco $root The root node of Aco Tree
     * @param array $plugins list of App plugins
     * @return void
     */
    protected function _processPlugins($root, array $plugins = [])
    {
        foreach ($plugins as $plugin) {
            $controllers = $this->getControllerList($plugin);
            $pluginAlias = $this->_pluginAlias($plugin);
            $path = [
                $this->rootNode,
                $pluginAlias,
            ];
            $path = implode('/', Hash::filter($path));
            $pathNode = $this->_checkNode($path, $pluginAlias, $root->id);
            $this->foundACOs[$root->id][] = $pluginAlias;

            if (isset($this->foundACOs[$pathNode->id])) {
                $this->foundACOs[$pathNode->id] += $this->_updateControllers($pathNode, $controllers, $plugin);
            } else {
                $this->foundACOs[$pathNode->id] = $this->_updateControllers($pathNode, $controllers, $plugin);
            }

            if (isset($this->pluginPrefixes[$plugin])) {
                foreach (array_keys($this->pluginPrefixes[$plugin]) as $prefix) {
                    $path = [
                        $this->rootNode,
                        $pluginAlias,
                    ];
                    $path = implode('/', Hash::filter($path));
                    $pluginNode = $this->_checkNode($path, $pluginAlias, $root->id);
                    $this->foundACOs[$root->id][] = $pluginAlias;

                    $path = [
                        $this->rootNode,
                        $pluginAlias,
                        $prefix,
                    ];
                    $path = implode('/', Hash::filter($path));
                    $pathNode = $this->_checkNode($path, $prefix, $pluginNode->id);
                    $this->foundACOs[$pluginNode->id][] = $prefix;

                    $controllers = $this->getControllerList($plugin, $prefix);
                    if (isset($this->foundACOs[$pathNode->id])) {
                        $this->foundACOs[$pathNode->id] += $this->_updateControllers($pathNode, $controllers, $pluginAlias, $prefix);
                    } else {
                        $this->foundACOs[$pathNode->id] = $this->_updateControllers($pathNode, $controllers, $pluginAlias, $prefix);
                    }
                }
            }
        }
    }

    /**
     * Updates a collection of controllers.
     *
     * @param array $root Array or ACO information for root node.
     * @param array $controllers Array of Controllers
     * @param string $plugin Name of the plugin you are making controllers for.
     * @param string $prefix Name of the prefix you are making controllers for.
     * @return array
     */
    protected function _updateControllers($root, array $controllers, string $plugin = '', string $prefix = '')
    {
        $pluginPath = $this->_pluginAlias($plugin);

        // look at each controller
        $controllersNames = [];
        foreach ($controllers as $controller) {
            $tmp = explode('/', $controller);
            $controllerName = str_replace('Controller.php', '', array_pop($tmp));
            // Always skip the App controller
            if ($controllerName == 'App') {
                continue;
            }
            // Skip anything that is not a concrete controller
            $namespace = $this->_getNamespace($controller, $pluginPath, $prefix);
            if (!(new \ReflectionClass($namespace))->isInstantiable()) {
                continue;
            }
            $controllersNames[] = $controllerName;
            $path = [
                $this->rootNode,
                $pluginPath,
                $prefix,
                $controllerName,
            ];
            $path = implode('/', Hash::filter($path));
            $controllerNode = $this->_checkNode($path, $controllerName, $root->id);
            $this->_checkMethods($controller, $controllerName, $controllerNode, $pluginPath, $prefix);
        }

        return $controllersNames;
    }

    /**
     * Get a list of controllers in the app and plugins.
     *
     * Returns an array of path => import notation.
     *
     * @param string $plugin Name of plugin to get controllers for
     * @param string $prefix Name of prefix to get controllers for
     * @return array
     */
    public function getControllerList($plugin = null, $prefix = null)
    {
        if (!$plugin) {
            $path = App::classPath('Controller' . (empty($prefix) ? '' : DS . Inflector::camelize($prefix)));
            $controllers = FsUtils::find($path[0], '.*Controller\.php');
        } else {
            $path = Plugin::classPath($plugin) . 'Controller' . DS. (empty($prefix) ? '' : DS . Inflector::camelize($prefix));
            $controllers = FsUtils::find($path, '.*Controller\.php');
        }

        return $controllers;
    }

    /**
     * Check a node for existance, create it if it doesn't exist.
     *
     * @param string $path The path to check
     * @param string $alias The alias to create
     * @param int $parentId The parent id to use when creating.
     * @return array Aco Node array
     */
    protected function _checkNode($path, $alias, $parentId = null)
    {
        $node = $this->Aco->node($path);
        if (!$node) {
            $aliases = explode('/', $alias);
            foreach ($aliases as $newAlias) {
                $parentId = !empty($node) ? $node->id : $parentId;
                $data = [
                    'parent_id' => $parentId,
                    'model' => null,
                    'alias' => $newAlias,
                ];
                $entity = $this->Aco->newEntity($data);
                $node = $this->Aco->save($entity);
            }
            $this->out(__d('cake_acl', 'Created Aco node: <success>{0}</success>', $path));
        } else {
            $node = $node->first();
        }

        return $node;
    }

    /**
     * Get a list of registered callback methods
     *
     * @param string $className The class to reflect on.
     * @param string $pluginPath The plugin path.
     * @param string $prefixPath The prefix path.
     * @return array
     */
    protected function _getCallbacks($className, $pluginPath = null, $prefixPath = null)
    {
        $callbacks = [];
        $namespace = $this->_getNamespace($className, $pluginPath, $prefixPath);
        $reflection = new \ReflectionClass($namespace);
        if ($reflection->isAbstract()) {
            return $callbacks;
        }
        try {
            $method = $reflection->getMethod('implementedEvents');
        } catch (ReflectionException $e) {
            return $callbacks;
        }
        if (version_compare(phpversion(), '5.4', '>=')) {
            $object = $reflection->newInstanceWithoutConstructor();
        } else {
            $object = unserialize(
                sprintf('O:%d:"%s":0:{}', strlen($className), $className)
            );
        }
        $implementedEvents = $method->invoke($object);
        foreach ($implementedEvents as $event => $callable) {
            if (is_string($callable)) {
                $callbacks[] = $callable;
            }
            if (is_array($callable) && isset($callable['callable'])) {
                $callbacks[] = $callable['callable'];
            }
        }

        return $callbacks;
    }

    /**
     * Check and Add/delete controller Methods
     *
     * @param string $className The classname to check
     * @param string $controllerName The controller name
     * @param array $node The node to check.
     * @param string $pluginPath The plugin path to use.
     * @param string $prefixPath The prefix path to use.
     * @return bool
     */
    protected function _checkMethods($className, $controllerName, $node, $pluginPath = null, $prefixPath = null)
    {
        $excludes = $this->_getCallbacks($className, $pluginPath, $prefixPath);
        // Cake 5: Controller wymaga ServerRequest w konstruktorze - nazwa klasy wystarcza
        $baseMethods = get_class_methods(Controller::class);
        $namespace = $this->_getNamespace($className, $pluginPath, $prefixPath);
        $methods = get_class_methods($namespace);
        if ($methods == null) {
            $this->err(__d('cake_acl', 'Unable to get methods for {0}', $className));

            return false;
        }
        $actions = array_diff($methods, $baseMethods);
        $actions = array_diff($actions, $excludes);
        foreach ($actions as $key => $action) {
            if (strpos($action, '_', 0) === 0) {
                continue;
            }
            $path = [
                $this->rootNode,
                $pluginPath,
                $prefixPath,
                $controllerName,
                $action,
            ];
            $path = implode('/', Hash::filter($path));
            $this->_checkNode($path, $action, $node->id);
            $actions[$key] = $action;
        }
        if ($this->_clean) {
            $this->_cleaner($node->id, $actions);
        }

        return true;
    }

    /**
     * Recover an Acl Tree
     *
     * @return void
     */
    public function recover()
    {
        $type = Inflector::camelize($this->args[0]);
        // Cake 5.3: metody behaviora na instancji tabeli deprecated
        $this->Acl->{$type}->getBehavior('Tree')->recover();
        $this->out(__('Tree has been recovered, or tree did not need recovery.'));
    }

    /**
     * Get the namespace for a given class.
     *
     * @param string $className The class you want a namespace for.
     * @param string $pluginPath The plugin path.
     * @param string $prefixPath The prefix path.
     * @return string
     */
    protected function _getNamespace($className, $pluginPath = null, $prefixPath = null)
    {
        $namespace = preg_replace('/(.*)Controller\//', '', $className);
        $namespace = preg_replace('/\//', '\\', $namespace);
        $namespace = preg_replace('/\.php/', '', $namespace);
        $prefixPath = preg_replace('/\//', '\\', Inflector::camelize($prefixPath));
        if (!$pluginPath) {
            $rootNamespace = Configure::read('App.namespace');
        } else {
            $rootNamespace = preg_replace('/\//', '\\', $pluginPath);
        }
        $namespace = [
            $rootNamespace,
            'Controller',
            $prefixPath,
            $namespace,
        ];

        return implode('\\', Hash::filter($namespace));
    }

    /**
     * Build prefixes for App and Plugins based on configured routes
     *
     * @return void
     */
    protected function _buildPrefixes()
    {
        $routes = Router::routes();
        foreach ($routes as $key => $route) {
            if (isset($route->defaults['prefix'])) {
                $prefixes = explode('/', $route->defaults['prefix']);
                $prefix = implode('/', array_map(
                    'Cake\\Utility\\Inflector::camelize',
                    $prefixes
                ));
                if (!isset($route->defaults['plugin'])) {
                    $this->prefixes[$prefix] = true;
                } else {
                    $this->pluginPrefixes[$route->defaults['plugin']][$prefix] = true;
                }
            }
        }
    }

    /**
     * Delete unused ACOs.
     *
     * @param int $parentId Id of the parent node.
     * @param array $preservedItems list of items that will not be erased.
     * @return void
     */
    protected function _cleaner($parentId, $preservedItems = [])
    {
        $nodes = $this->Aco->find()->where(['parent_id' => $parentId]);
        $methodFlip = [];
        foreach ($preservedItems as $preservedItem) {
            $aliases = explode('/', $preservedItem);
            foreach ($aliases as $alias) {
                $methodFlip[$alias] = true;
            }
        }
        foreach ($nodes as $node) {
            if (!isset($methodFlip[$node->alias])) {
                $crumbs = $this->Aco->find('path', for: $node->id)->orderByAsc('lft');
                $path = null;
                foreach ($crumbs as $crumb) {
                    $path .= '/' . $crumb->alias;
                }
                $entity = $this->Aco->get($node->id);
                if ($this->Aco->delete($entity)) {
                    $this->out(__d('cake_acl', 'Deleted Aco node: <warning>{0}</warning> and all children', $path));
                }
            }
        }
    }

    /**
     * Get discovered app route prefixes
     *
     * @return array
     */
    public function getPrefixes()
    {
        return $this->prefixes;
    }

    /**
     * Get discovered plugin route prefixes
     *
     * @return array
     */
    public function getPluginPrefixes()
    {
        return $this->pluginPrefixes;
    }

    /**
     * Get the attached controller.
     *
     * @return \Cake\Controller\Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Get the attached shell.
     *
     * @return \Cake\Console\ConsoleIo
     */
    public function getShell()
    {
        return $this->Shell;
    }

    /**
     * Attach a shell for output.
     *
     * @param \Cake\Console\ConsoleIo $shell Shell to attach
     * @return void
     */
    public function setShell(ConsoleIo $shell)
    {
        $this->Shell = $shell;
    }
}
