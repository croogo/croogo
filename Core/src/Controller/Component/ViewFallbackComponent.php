<?php

namespace Croogo\Core\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Utility\Inflector;
use InvalidArgumentException;

class ViewFallbackComponent extends Component
{

	public function beforeRender(Event $event)
	{
		/** @var Controller $controller */
		$controller = $event->subject();

		$fallbackView = $this->__getDefaultFallbackView($controller);
		if (!$this->_getViewFileName($controller->view) && in_array($controller->request->action, ['edit', 'add'])) {
			$viewPaths = App::path('View', $controller->plugin);
			$themePath = $controller->theme ? App::themePath($controller->theme) : null;
			$searchPaths = array_merge((array)$themePath, $viewPaths);
			$view = $this->__findRequestedView($controller, $searchPaths);
			if (empty($view)) {
				$view = $fallbackView;
			}

			$controller->view = $view;
		}
	}


	/**
	 * Get Default Fallback View
	 *
	 * @return string
	 */
	private function __getDefaultFallbackView(Controller $controller) {
		$fallbackView = 'form';
		if (!empty($controller->request->params['prefix']) && $controller->request->params['prefix'] === 'admin') {
			$fallbackView = 'form';
		}
		return $fallbackView;
	}

	/**
	 * Search for existing view override in registered view paths
	 *
	 * @return string
	 */
	private function __findRequestedView(Controller $controller, $viewPaths) {
		if (empty($viewPaths)) {
			return false;
		}
		foreach ($viewPaths as $path) {
			$file = $controller->viewPath . DS . $controller->request->action . '.ctp';
			$requested = $path . $file;
			if (file_exists($requested)) {
				return $requested;
			} else {
				if (!$controller->plugin) {
					continue;
				}
				$requested = $path . 'Plugin' . DS . $controller->plugin . DS . $file;
				if (file_exists($requested)) {
					return $requested;
				}
			}
		}
		return false;
	}

	/**
	 * Returns filename of given action's template file (.ctp) as a string.
	 * CamelCased action names will be under_scored! This means that you can have
	 * LongActionNames that refer to long_action_names.ctp views.
	 *
	 * @param string|null $name Controller action to find template filename for
	 * @return string Template filename
	 * @throws \Cake\View\Exception\MissingTemplateException when a view file could not be found.
	 */
	protected function _getViewFileName($name = null)
	{
		$viewPath = $subDir = '';

		if ($this->subDir !== null) {
			$subDir = $this->subDir . DS;
		}
		if ($this->viewPath) {
			$viewPath = $this->viewPath . DS;
		}

		if ($name === null) {
			$name = $this->view;
		}

		list($plugin, $name) = $this->pluginSplit($name);
		$name = str_replace('/', DS, $name);

		if (strpos($name, DS) === false && $name[0] !== '.') {
			$name = $viewPath . $subDir . Inflector::underscore($name);
		} elseif (strpos($name, DS) !== false) {
			if ($name[0] === DS || $name[1] === ':') {
				if (is_file($name)) {
					return $name;
				}
				$name = trim($name, DS);
			} elseif (!$plugin || $this->viewPath !== $this->name) {
				$name = $viewPath . $subDir . $name;
			} else {
				$name = DS . $subDir . $name;
			}
		}

		foreach ($this->_paths($plugin) as $path) {
			if (file_exists($path . $name . $this->_ext)) {
				return $this->_checkFilePath($path . $name . $this->_ext, $path);
			}
		}

		return false;
	}

	/**
	 * Splits a dot syntax plugin name into its plugin and filename.
	 * If $name does not have a dot, then index 0 will be null.
	 * It checks if the plugin is loaded, else filename will stay unchanged for filenames containing dot
	 *
	 * @param string $name The name you want to plugin split.
	 * @param bool $fallback If true uses the plugin set in the current Request when parsed plugin is not loaded
	 * @return array Array with 2 indexes. 0 => plugin name, 1 => filename
	 */
	public function pluginSplit($name, $fallback = true)
	{
		$plugin = null;
		list($first, $second) = pluginSplit($name);
		if (Plugin::loaded($first) === true) {
			$name = $second;
			$plugin = $first;
		}
		if (isset($this->plugin) && !$plugin && $fallback) {
			$plugin = $this->plugin;
		}
		return [$plugin, $name];
	}

	/**
	 * Check that a view file path does not go outside of the defined template paths.
	 *
	 * Only paths that contain `..` will be checked, as they are the ones most likely to
	 * have the ability to resolve to files outside of the template paths.
	 *
	 * @param string $file The path to the template file.
	 * @param string $path Base path that $file should be inside of.
	 * @return string The file path
	 * @throws \InvalidArgumentException
	 */
	protected function _checkFilePath($file, $path)
	{
		if (strpos($file, '..') === false) {
			return $file;
		}
		$absolute = realpath($file);
		if (strpos($absolute, $path) !== 0) {
			throw new InvalidArgumentException(sprintf(
				'Cannot use "%s" as a template, it is not within any view template path.',
				$file
			));
		}
		return $absolute;
	}


	/**
	 * Return all possible paths to find view files in order
	 *
	 * @param string|null $plugin Optional plugin name to scan for view files.
	 * @param bool $cached Set to false to force a refresh of view paths. Default true.
	 * @return array paths
	 */
	protected function _paths($plugin = null, $cached = true)
	{
		if ($cached === true) {
			if ($plugin === null && !empty($this->_paths)) {
				return $this->_paths;
			}
			if ($plugin !== null && isset($this->_pathsForPlugin[$plugin])) {
				return $this->_pathsForPlugin[$plugin];
			}
		}
		$viewPaths = App::path('Template');
		$pluginPaths = $themePaths = [];
		if (!empty($plugin)) {
			for ($i = 0, $count = count($viewPaths); $i < $count; $i++) {
				$pluginPaths[] = $viewPaths[$i] . 'Plugin' . DS . $plugin . DS;
			}
			$pluginPaths = array_merge($pluginPaths, App::path('Template', $plugin));
		}

		if (!empty($this->theme)) {
			$themePaths = App::path('Template', Inflector::camelize($this->theme));

			if ($plugin) {
				for ($i = 0, $count = count($viewPaths); $i < $count; $i++) {
					array_unshift($themePaths, $themePaths[$i] . 'Plugin' . DS . $plugin . DS);
				}
			}
		}

		$paths = array_merge(
			$themePaths,
			$pluginPaths,
			$viewPaths,
			[dirname(__DIR__) . DS . 'Template' . DS]
		);

		if ($plugin !== null) {
			return $this->_pathsForPlugin[$plugin] = $paths;
		}
		return $this->_paths = $paths;
	}

}
