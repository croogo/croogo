<?php

App::uses('Router', 'Routing');

/**
 * CroogoRouter
 *
 * NOTE: Do not use this class as a substitute of Router class.
 * Use it only for CroogoRouter::connect()
 *
 * @package  Croogo.Croogo.Lib
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoRouter {

/**
 * If Translate plugin is active,
 * an extra Route will be created for locale-based URLs
 *
 * For example,
 * http://yoursite.com/blog/post-title, and
 * http://yoursite.com/eng/blog/post-title
 *
 * Returns this object's routes array. Returns false if there are no routes available.
 *
 * @param string $route         An empty string, or a route string "/"
 * @param array $default        NULL or an array describing the default route
 * @param array $params         An array matching the named elements in the
 *                              route to regular expressions which that element
 *                              should match.
 * @return array                Array of routes
 * @see Router::connect()
 * @throws RouterException
 */
	public static function connect($route, $default = array(), $params = array()) {
		$localizedRoute = $route == '/' ? '' : $route;
		if (Configure::read('Translate')) {
			Router::connect('/:locale' . $localizedRoute, $default, array_merge(array('locale' => '[a-z]{3}'), $params));
		}
		return Router::connect($route, $default, $params);
	}

/**
 * If you want your non-routed controler actions (like /users/add) to support locale based urls,
 * this method must be called AFTER all the routes.
 *
 * @return void
 */
	public static function localize() {
		if (Configure::read('Translate')) {
			Router::connect('/:locale/:controller/:action/*', array(), array('locale' => '[a-z]{3}'));
		}
	}

/**
 * Load plugin routes
 *
 * @return void
 */
	public static function plugins() {
		$pluginRoutes = Configure::read('Hook.routes');
		if (!$pluginRoutes || !is_array(Configure::read('Hook.routes'))) {
			return;
		}

		$plugins = Configure::read('Hook.routes');
		foreach ($plugins as $plugin) {
			$path = App::pluginPath($plugin) . 'Config' . DS . 'routes.php';
			if (file_exists($path)) {
				include $path;
			}
		}
	}

/**
 * Routes for content types
 *
 * @param string $alias
 * @return void
 */
	public static function contentType($alias) {
		CroogoRouter::connect('/' . $alias, array(
			'plugin' => 'nodes', 'controller' => 'nodes',
			'action' => 'index', 'type' => $alias
		));
		CroogoRouter::connect('/' . $alias . '/archives/*', array(
			'plugin' => 'nodes', 'controller' => 'nodes',
			'action' => 'index', 'type' => $alias
		));
		CroogoRouter::connect('/' . $alias . '/:slug', array(
			'plugin' => 'nodes', 'controller' => 'nodes',
			'action' => 'view', 'type' => $alias
		));
		CroogoRouter::connect('/' . $alias . '/term/:slug/*', array(
			'plugin' => 'nodes', 'controller' => 'nodes',
			'action' => 'term', 'type' => $alias
		));
	}

/**
 * Apply routes for content types with routes enabled
 *
 * @return void
 */
	public static function routableContentTypes() {
		try {
			$types = ClassRegistry::init('Taxonomy.Type')->find('all', array(
				'cache' => array(
					'name' => 'types',
					'config' => 'croogo_types',
				),
			));
			foreach ($types as $type) {
				if (isset($type['Params']['routes']) && $type['Params']['routes']) {
					CroogoRouter::contentType($type['Type']['alias']);
				}
			}
		}
		catch (MissingConnectionException $e) {
			CakeLog::write('critical', __d('croogo', 'Unable to get routeable content types: %s', $e->getMessage()));
		}
	}
}
