<?php

namespace Croogo\Core;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Exception\MissingConnectionException;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;

use Croogo\Routing\Route\ApiRoute;

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
class CroogoRouter
{

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
    public static function connect($route, $default = [], $params = [], $options = [])
    {
        return self::__connect($route, $default, $params, $options);
    }

/**
 *
 * @see Router::connect()
 */
    private static function __connect($route, $default = [], $params = [], $options = [])
    {
        $options = Hash::merge(['promote' => false], $options);
        $localizedRoute = $route == '/' ? '' : $route;
        if (Plugin::loaded('Translate')) {
            Router::connect('/:locale' . $localizedRoute, $default, array_merge(['locale' => '[a-z]{3}'], $params));
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
 * @see Request::addDetector()
 * @param $request Request Request object
 * @return bool True when request contains the necessary route parameters
 */
    public static function isApiRequest(Request $request)
    {
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
 * @see Request::addDetector()
 * @param $request Request Request object
 * @return boolean True when request is from a whitelisted IP Address
 */
    public static function isWhitelistedRequest(Request $request)
    {
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
    public static function mapResources($controller, $options = [])
    {
        $options = array_merge([
            'connectOptions' => [
                'Croogo\Core\Routing\Route\ApiRoute',
            ],
        ], $options);
        return Router::mapResources($controller, $options);
    }

/**
 * If you want your non-routed controler actions (like /users/add) to support locale based urls,
 * this method must be called AFTER all the routes.
 *
 * @return void
 */
    public static function localize()
    {
        if (Plugin::loaded('Translate')) {
            Router::connect('/:locale/:plugin/:controller/:action/*', [], ['locale' => '[a-z]{3}']);
            Router::connect('/:locale/:controller/:action/*', [], ['locale' => '[a-z]{3}']);
        }
    }

/**
 * Load plugin routes
 *
 * @return void
 */
    public static function plugins()
    {
        $pluginRoutes = Configure::read('Hook.routes');
        if (!$pluginRoutes || !is_array(Configure::read('Hook.routes'))) {
            return;
        }

        $plugins = Configure::read('Hook.routes');
        foreach ($plugins as $plugin) {
            $path = App::pluginPath($plugin) . 'config' . DS . 'routes.php';
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
    public static function contentType($alias, $routeBuilder)
    {
        $routeBuilder->connect('/' . $alias, [
            'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes',
            'action' => 'index', 'type' => $alias
        ]);
        $routeBuilder->connect('/' . $alias . '/archives/*', [
            'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes',
            'action' => 'index', 'type' => $alias
        ]);
        $routeBuilder->connect('/' . $alias . '/:slug', [
            'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes',
            'action' => 'view', 'type' => $alias
        ]);
        $routeBuilder->connect('/' . $alias . '/term/:slug/*', [
            'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes',
            'action' => 'term', 'type' => $alias
        ]);
    }

/**
 * Apply routes for content types with routes enabled
 *
 * @return void
 */
    public static function routableContentTypes($routeBuilder)
    {
        try {
            $types = TableRegistry::get('Croogo/Taxonomy.Types')->find('all', [
                'cache' => [
                    'name' => 'types',
                    'config' => 'croogo_types',
                ],
            ]);
            foreach ($types as $type) {
                if (isset($type->params['routes']) && $type->params['routes']) {
                    CroogoRouter::contentType($type->alias, $routeBuilder);
                }
            }
        } catch (MissingConnectionException $e) {
            Log::write('critical', __d('croogo', 'Unable to get routeable content types: %s', $e->getMessage()));
        }
    }

/**
 * Setup Site.home_url
 *
 * @return void
 */
    public static function routes()
    {
        $homeUrl = Configure::read('Site.home_url');
        if ($homeUrl && strpos($homeUrl, ':') !== false) {
            $converter = new StringConverter();
            $url = $converter->linkStringToArray($homeUrl);
            CroogoRouter::connect('/', $url, [], ['promote' => true]);
        }
        Plugin::routes();
    }
}
