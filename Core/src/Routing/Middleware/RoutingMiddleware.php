<?php
declare(strict_types=1);

namespace Croogo\Core\Routing\Middleware;

use Cake\Core\PluginApplicationInterface;
use Cake\Routing\Middleware\RoutingMiddleware as CakeRoutingMiddleware;
use Cake\Routing\RouteCollection;
use Croogo\Core\Routing\Router;

/**
 * @inheritdoc
 *
 * @property \Cake\Core\PluginApplicationInterface|\Cake\Routing\RoutingApplicationInterface $app
 */
class RoutingMiddleware extends CakeRoutingMiddleware
{

    protected function prepareRouteCollection(): RouteCollection
    {
        $builder = Router::createRouteBuilder('/');
        $this->app->routes($builder);
        if ($this->app instanceof PluginApplicationInterface) {
            $this->app->pluginRoutes($builder);
        }

        return Router::getRouteCollection();
    }

}
