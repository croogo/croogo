<?php

App::uses('ApiRoute', 'Croogo.Routing/Route');
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
	public static function connect($route, $default = array(), $params = array(), $options = array()) {
		return self::__connect($route, $default, $params, $options);
	}

/**
 *
 * @see Router::connect()
 */
	private static function __connect($route, $default = array(), $params = array(), $options = array()) {
		$options = Hash::merge(array('promote' => false), $options);
		$localizedRoute = $route == '/' ? '' : $route;
		if (CakePlugin::loaded('Translate')) {
			Router::connect('/:locale' . $localizedRoute, $default, array_merge(array('locale' => '[a-z]{3}'), $params));
			if ($options['promote']) {
				Router::promote();
			}
		}
		$return = Router::connect($route, $default, $params);
		if ($options['promote']) {
			Router::promote();
		}
		return $return;
	}

/**
 * Check wether request is a API call.
 *
 * @see CakeRequest::addDetector()
 * @param $request CakeRequest Request object
 * @return bool True when request contains the necessary route parameters
 */
	public static function isApiRequest(CakeRequest $request) {
		if (!$request) {
			return false;
		}
		if (empty($request['api']) || empty($request['prefix'])) {
			return false;
		}
		if ($request['api'] !== Configure::read('Croogo.Api.path')) {
			return false;
		}
		return true;
	}

/**
 * Check wether request is from a whitelisted IP address
 *
 * @see CakeRequest::addDetector()
 * @param $request CakeRequest Request object
 * @return boolean True when request is from a whitelisted IP Address
 */
	public static function isWhitelistedRequest(CakeRequest $request) {
		if (!$request) {
			return false;
		}
		$clientIp = $request->clientIp();
		$whitelist = array_map(
			'trim',
			(array)explode(',', Configure::read('Site.ipWhitelist'))
		);
		return in_array($clientIp, $whitelist);
	}

/**
 * Creates REST resource routes for the given controller(s).
 *
 * @param string|array $controller string or array of controller names
 * @return array Array of mapped resources
 * @see Router::mapResources()
 */
	public static function mapResources($controller, $options = array()) {
		$options = array_merge(array(
			'routeClass' => 'ApiRoute',
		), $options);
		static $defaultRouteClass;
		if (empty($defaultRouteClass)) {
			$defaultRouteClass = Router::defaultRouteClass();
		}
		Router::defaultRouteClass('ApiRoute');
		$routes = Router::mapResources($controller, $options);
		Router::defaultRouteClass($defaultRouteClass);
		return $routes;
	}

/**
 * If you want your non-routed controler actions (like /users/add) to support locale based urls,
 * this method must be called AFTER all the routes.
 *
 * @return void
 */
	public static function localize() {
		if (CakePlugin::loaded('Translate')) {
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

/**
 * Setup Site.home_url
 *
 * @return void
 */
	public static function routes() {
		$homeUrl = Configure::read('Site.home_url');
		if ($homeUrl && strpos($homeUrl, ':') !== false) {
			$converter = new StringConverter();
			$url = $converter->linkStringToArray($homeUrl);
			CroogoRouter::connect('/', $url, array(), array('promote' => true));
		}
		CakePlugin::routes();
	}

}
