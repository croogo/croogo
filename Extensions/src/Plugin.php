<?php

namespace Croogo\Extensions;

use Cake\Core\BasePlugin;
use Cake\Core\Plugin as CakePlugin;
use Cake\Core\PluginApplicationInterface;

class Plugin extends BasePlugin
{

    public function bootstrap(PluginApplicationInterface $app)
    {
        if (!CakePlugin::isLoaded('Migrations')) {
            $app->addPlugin('Migrations', ['autoload' => true, 'classBase' => false]);
        }
        if (!CakePlugin::isLoaded('Croogo/Settings')) {
            $app->addPlugin('Croogo/Settings', ['bootstrap' => true, 'routes' => true]);
        }
        if (!CakePlugin::isLoaded('Search')) {
            $app->addPlugin('Search', ['autoload' => true, 'classBase' => false]);
        }
    }
}
