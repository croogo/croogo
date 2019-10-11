<?php

namespace Croogo\Extensions\Event;

use Cake\Core\Plugin;
use Cake\Event\EventListenerInterface;
use Croogo\Core\PluginManager;

/**
 * ExtensionsEventHandler
 *
 * @package  Croogo.Extensions.Event
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExtensionsEventHandler implements EventListenerInterface
{

    /**
     * implementedEvents
     */
    public function implementedEvents()
    {
        return [
            'Croogo.bootstrapComplete' => [
                'callable' => 'onBootstrapComplete',
            ],
            'Croogo.beforeSetupAdminData' => [
                'callable' => 'onBeforeSetupAdminData',
            ],
            'Croogo.setupAdminData' => [
                'callable' => 'onSetupAdminData',
            ],
        ];
    }

    /**
     * Before Setup admin data
     */
    public function onBeforeSetupAdminData($event)
    {
        $plugins = Plugin::loaded();
        $config = 'config' . DS . 'admin.php';
        foreach ($plugins as $plugin) {
            $file = Plugin::path($plugin) . $config;
            if (file_exists($file)) {
                require $file;
            }
        }
    }

    /**
     * Setup admin data
     */
    public function onSetupAdminData($event)
    {
        $plugins = Plugin::loaded();
        $config = 'config' . DS . 'admin_menu.php';
        foreach ($plugins as $plugin) {
            $file = Plugin::path($plugin) . $config;
            if (file_exists($file)) {
                require $file;
            }
        }
    }

    /**
     * onBootstrapComplete
     */
    public function onBootstrapComplete($event)
    {
        PluginManager::cacheDependencies();
    }
}
