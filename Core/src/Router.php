<?php

namespace Croogo\Core;

use Cake\Core\Configure;
use Cake\Database\Exception\MissingConnectionException;
use Cake\Log\Log;
use Cake\Network\Request;
use Cake\ORM\TableRegistry;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router as CakeRouter;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Router
 *
 * NOTE: Do not use this class as a substitute of Router class.
 * Use it only for Router::connect()
 *
 * @package  Croogo.Croogo.Lib
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Router extends CakeRouter
{

/**
 * Helper method to setup both default and localized route
 */
    public static function build(RouteBuilder $builder, $path, $defaults, $options = [])
    {
        if (Plugin::loaded('Croogo/Translate')) {
            $languages = Configure::read('I18n.languages');
            $i18nPath = '/:lang' . $path;
            $i18nOptions = array_merge($options, ['lang' => implode('|', $languages)]);
            $builder->connect($i18nPath, $defaults, $i18nOptions);
        }
        $builder->connect($path, $defaults, $options);
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

        return static::mapResources($controller, $options);
    }

/**
 * If you want your non-routed controler actions (like /users/add) to support locale based urls,
 * this method must be called AFTER all the routes.
 *
 * @return void
 */
    public static function localize()
    {
        if (Plugin::loaded('Croogo/Translate')) {
            static::connect('/:locale/:plugin/:controller/:action/*', [], ['locale' => '[a-z]{3}']);
            static::connect('/:locale/:controller/:action/*', [], ['locale' => '[a-z]{3}']);
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
        static::build($routeBuilder, '/' . $alias, [
            'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes',
            'action' => 'index', 'type' => $alias
        ]);
        static::build($routeBuilder, '/' . $alias . '/archives/*', [
            'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes',
            'action' => 'index', 'type' => $alias
        ]);
        static::build($routeBuilder, '/' . $alias . '/:slug', [
            'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes',
            'action' => 'view', 'type' => $alias
        ]);
        static::build($routeBuilder, '/' . $alias . '/term/:slug/*', [
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
                    static::contentType($type->alias, $routeBuilder);
                }
            }
        } catch (MissingConnectionException $e) {
            Log::write('critical', __d('croogo', 'Unable to get routeable content types: %s', $e->getMessage()));
        }
    }

    public static function url($url = null, $full = false)
    {
        if ($url instanceof Link) {
            $url = $url->getUrl();
        }

        return parent::url($url, $full);
    }

    public static function getActionPath(Request $request, $encode = false)
    {
        $plugin = $request->param('plugin');
        $prefix = $request->param('prefix');
        $val  = $plugin ? $plugin . '.' : null;
        $val .= $prefix ? Inflector::camelize($prefix) . '/' : null;
        $val .= $request->param('controller') . '/' . $request->param('action');
        if ($encode) {
            $val = base64_encode($val);
        }
        return $val;
    }

    /**
 * Setup Site.home_url
 *
 * @return void
 */
//    public static function routes()
//    {
//        $homeUrl = Configure::read('Site.home_url');
//        if ($homeUrl && strpos($homeUrl, ':') !== false) {
//            $converter = new StringConverter();
//            $url = $converter->linkStringToArray($homeUrl);
//            Router::connect('/', $url, [], ['promote' => true]);
//        }
//        Plugin::routes();
//    }
}
