<?php
declare(strict_types=1);

namespace Croogo\Core;

use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Routing\RouteBuilder;
use Cake\Utility\Security;
use Croogo\Core\Routing\Router;

class Plugin extends BasePlugin
{

    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);

        timerStart('Croogo bootstrap');
        PluginManager::setup($app);
        PluginManager::croogoBootstrap($app);
        timerStop('Croogo bootstrap');

        // Load Install plugin
        $salted = Security::getSalt() !== '__SALT__';
        if (!Configure::read('Croogo.installed') || !$salted) {
            $app->addPlugin('Croogo/Install', ['routes' => true, 'bootstrap' => true]);
        }
    }

    /**
     * @param \Cake\Routing\RouteBuilder $routes The route builder to update.
     * @return void
     */
    public function routes(RouteBuilder $routes): void
    {
        parent::routes($routes);
        Router::homepage();
    }

}
