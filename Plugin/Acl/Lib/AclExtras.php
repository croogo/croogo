<?php
/**
 * Acl Extras Shell.
 *
 * Enhances the existing Acl Shell with a few handy functions
 *
 * Copyright 2008, Mark Story.
 * 694B The Queensway
 * toronto, ontario M8Y 1K9
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2008-2010, Mark Story.
 * @link http://mark-story.com
 * @author Mark Story <mark@mark-story.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */

App::uses('Controller', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('AclComponent', 'Controller/Component');
App::uses('DbAcl', 'Model');
App::uses('Shell', 'Console');

/**
 * Shell for ACO extras
 *
 * @package		acl_extras
 * @subpackage	acl_extras.Console.Command
 */
class AclExtras extends Object {

/**
 * Contains instance of AclComponent
 *
 * @var AclComponent
 * @access public
 */
	public $Acl;

/**
 * Contains arguments parsed from the command line.
 *
 * @var array
 * @access public
 */
	public $args;

/**
 * Contains database source to use
 *
 * @var string
 * @access public
 */
	public $dataSource = 'default';

/**
 * Root node name.
 *
 * @var string
 **/
	public $rootNode = 'controllers';

/**
 * Internal Clean Actions switch
 *
 * @var boolean
 **/
	protected $_clean = false;

/**
 * array of output messages
 */
	public $output = array();

/**
 * array of error messages
 */
	public $errors = array();

/**
 * count of newly created ACOs
 */
	public $created = 0;

/**
 * Start up And load Acl Component / Aco model
 *
 * @return void
 **/
	public function startup($controller = null) {
		if (!$controller) {
			$controller = new Controller(new CakeRequest());
		}
		$collection = new ComponentCollection();
		$this->Acl = new AclComponent($collection);
		$this->Acl->startup($controller);
		$this->Aco = $this->Acl->Aco;
		$this->controller = $controller;
	}

	public function out($msg) {
		$this->output[] = $msg;
		if (!empty($this->controller->Session)) {
			$this->controller->Session->setFlash($msg);
		} elseif (isset($this->Shell)) {
			return $this->Shell->out($msg);
		}
	}

	public function err($msg) {
		$this->errors[] = $msg;
		if (!empty($this->controller->Session)) {
			$this->controller->Session->setFlash($msg);
		} elseif (isset($this->Shell)) {
			return $this->Shell->err($msg);
		}
	}

/**
 * Sync the ACO table
 *
 * @return void
 **/
	public function aco_sync($params = array()) {
		$this->_clean = true;
		return $this->aco_update($params);
	}

/**
 * Sync the ACO table for contents
 *
 * @return bool
 */
	public function aco_update_contents($params = array()) {
		list($plugin, $model) = pluginSplit($this->args[0]);
		App::uses($model, $plugin . '.Model');
		if (!class_exists($model)) {
			$this->err(__('Model %s cannot be found.', $model));
			return false;
		}

		$Model = ClassRegistry::init($model);
		$primaryKey = $Model->primaryKey;
		$rows = $Model->find('all');

		$root = $this->_checkNode('contents', 'contents', null);
		foreach ($rows as $row) {
			$alias = sprintf('%s.%s', $model, $row[$model][$primaryKey]);
			$path = sprintf('contents/%s', $alias);
			$acoNode = $this->Aco->node($path);
			if ($acoNode) {
				continue;
			}
			$this->Aco->create(array(
				'parent_id' => $root['Aco']['id'],
				'model' => $model,
				'alias' => $alias,
				'foreign_key' => $row[$model][$primaryKey],
				));
			$acoNode = $this->Aco->save();
			$this->out(__('Created Aco node: %s', $path), 1, Shell::VERBOSE);
		}
		return true;
	}

/**
 * Updates the Aco Tree with new controller actions.
 *
 * @return void
 **/
	public function aco_update($params = array()) {
		$root = $this->_checkNode($this->rootNode, $this->rootNode, null);

		if (empty($params['plugin'])) {
			$controllers = $this->getControllerList();
			$this->_updateControllers($root, $controllers);
			$plugins = CakePlugin::loaded();
		} else {
			$plugin = $params['plugin'];
			if (!in_array($plugin, App::objects('plugin')) || !CakePlugin::loaded($plugin)) {
				$this->err(__('<error>Plugin %s not found or not activated</error>', $plugin));
				return false;
			}
			$plugins = array($params['plugin']);
		}

		foreach ($plugins as $plugin) {
			$controllers = $this->getControllerList($plugin);

			$path = $this->rootNode . '/' . $plugin;
			$pluginRoot = $this->_checkNode($path, $plugin, $root['Aco']['id']);
			$this->_updateControllers($pluginRoot, $controllers, $plugin);
		}
		if ($this->_clean) {
			$this->out(__('<success>Aco Sync Complete</success>'));
		} else {
			$this->out(__('<success>Aco Update Complete</success>'));
		}
		return true;
	}

/**
 * Updates a collection of controllers.
 *
 * @param array $root Array or ACO information for root node.
 * @param array $controllers Array of Controllers
 * @param string $plugin Name of the plugin you are making controllers for.
 * @return void
 */
	protected function _updateControllers($root, $controllers, $plugin = null) {
		$dotPlugin = $pluginPath = $plugin;
		if ($plugin) {
			$dotPlugin .= '.';
			$pluginPath .= '/';
		}
		$appIndex = array_search($plugin . 'AppController', $controllers);
		if ($appIndex !== false) {
			App::uses($plugin . 'AppController', $dotPlugin . 'Controller');
			unset($controllers[$appIndex]);
		}
		// look at each controller
		foreach ($controllers as $controller) {
			App::uses($controller, $dotPlugin . 'Controller');
			$controllerName = preg_replace('/Controller$/', '', $controller);

			$path = $this->rootNode . '/' . $pluginPath . $controllerName;
			$controllerNode = $this->_checkNode($path, $controllerName, $root['Aco']['id']);
			$this->_checkMethods($controller, $controllerName, $controllerNode, $pluginPath);
		}
		if ($this->_clean) {
			if (!$plugin) {
				$controllers = array_merge($controllers, App::objects('plugin', null, false));
			}
			$controllerFlip = array_flip($controllers);

			$this->Aco->id = $root['Aco']['id'];
			$controllerNodes = $this->Aco->children(null, true);
			foreach ($controllerNodes as $ctrlNode) {
				$alias = $ctrlNode['Aco']['alias'];
				$name = $alias . 'Controller';
				if (!isset($controllerFlip[$name]) && !isset($controllerFlip[$alias])) {
					if ($this->Aco->delete($ctrlNode['Aco']['id'])) {
						$this->out(__(
							'Deleted %s and all children',
							$this->rootNode . '/' . $ctrlNode['Aco']['alias']
						), 1, Shell::VERBOSE);
					}
				}
			}
		}
	}

/**
 * Get a list of controllers in the app and plugins.
 *
 * Returns an array of path => import notation.
 *
 * @param string $plugin Name of plugin to get controllers for
 * @return array
 **/
	public function getControllerList($plugin = null) {
		$excludes = array('CakeErrorController');
		if (!$plugin) {
			$controllers = App::objects('Controller', null, false);
		} else {
			$controllers = App::objects($plugin . '.Controller', null, false);
		}
		$controllers = array_diff($controllers, $excludes);
		return $controllers;
	}

/**
 * Check a node for existance, create it if it doesn't exist.
 *
 * @param string $path
 * @param string $alias
 * @param int $parentId
 * @return array Aco Node array
 */
	protected function _checkNode($path, $alias, $parentId = null) {
		$node = $this->Aco->node($path);
		if (!$node) {
			$this->Aco->create(array('parent_id' => $parentId, 'model' => null, 'alias' => $alias));
			$node = $this->Aco->save();
			$node['Aco']['id'] = $this->Aco->id;
			$this->created++;
			$this->out(__('Created Aco node: <success>%s</success>', $path), 1, Shell::VERBOSE);
		} else {
			$node = $node[0];
		}
		return $node;
	}

/**
 * Check and Add/delete controller Methods
 *
 * @param string $controller
 * @param array $node
 * @param string $plugin Name of plugin
 * @return void
 */
	protected function _checkMethods($className, $controllerName, $node, $pluginPath = false) {
		$excludes = array('afterConstruct', 'securityError');
		$baseMethods = get_class_methods('Controller');
		$actions = get_class_methods($className);
		$methods = array_diff($actions, $baseMethods);
		$methods = array_diff($methods, $excludes);
		foreach ($methods as $action) {
			if (strpos($action, '_', 0) === 0) {
				continue;
			}
			$path = $this->rootNode . '/' . $pluginPath . $controllerName . '/' . $action;
			$this->_checkNode($path, $action, $node['Aco']['id']);
		}

		if ($this->_clean) {
			$actionNodes = $this->Aco->children($node['Aco']['id']);
			$methodFlip = array_flip($methods);
			foreach ($actionNodes as $action) {
				if (!isset($methodFlip[$action['Aco']['alias']])) {
					$this->Aco->id = $action['Aco']['id'];
					if ($this->Aco->delete()) {
						$path = $this->rootNode . '/' . $controllerName . '/' . $action['Aco']['alias'];
						$this->out(__('Deleted Aco node: <warning>%s</warning>', $path), 1, Shell::VERBOSE);
					}
				}
			}
		}
		return true;
	}

/**
 * Verify a Acl Tree
 *
 * @param string $type The type of Acl Node to verify
 * @access public
 * @return void
 */
	public function verify() {
		$type = Inflector::camelize($this->args[0]);
		$return = $this->Acl->{$type}->verify();
		if ($return === true) {
			$this->out(__('<success>Tree is valid and strong</success>'));
		} else {
			$this->err(print_r($return, true));
			return false;
		}
	}

/**
 * Recover an Acl Tree
 *
 * @param string $type The Type of Acl Node to recover
 * @access public
 * @return void
 */
	public function recover() {
		$type = Inflector::camelize($this->args[0]);
		$return = $this->Acl->{$type}->recover();
		if ($return === true) {
			$this->out(__('Tree has been recovered, or tree did not need recovery.'));
		} else {
			$this->err(__('<error>Tree recovery failed.</error>'));
			return false;
		}
	}

}
