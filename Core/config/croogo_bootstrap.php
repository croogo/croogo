<?php

namespace Croogo\Core\Config;

use Aura\Intl\Package;

use Cake\Cache\Cache;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\I18n\I18n;
use Cake\Log\Log;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

use Croogo\Core\Croogo;
use Croogo\Core\Cache\CroogoCache;
use Croogo\Core\Status;
use Croogo\Core\Event\CroogoEventManager;
use Croogo\Extensions\CroogoPlugin;
use Croogo\Settings\Configure\Engine\DatabaseConfig;

/**
 * Default Acl plugin.  Custom Acl plugin should override this value.
 */
Configure::write('Site.acl_plugin', 'Acl');

/**
 * Default API Route Prefix. This can be overriden in settings.
 */
Configure::write('Croogo.Api.path', 'api');

/**
 * Admin theme
 */
//Configure::write('Site.admin_theme', 'sample');

/**
 * Cache configuration
 */
//debug(Configure::read());exit();
$defaultEngine = Cache::config('default')['className'];
$defaultPrefix = Configure::read('Cache.defaultPrefix');
$cacheConfig = [
    'duration' => '+1 hour',
    'path' => CACHE . 'queries' . DS,
    'engine' => $defaultEngine,
    'prefix' => $defaultPrefix,
];
Configure::write('Croogo.Cache.defaultEngine', $defaultEngine);
Configure::write('Croogo.Cache.defaultPrefix', $defaultPrefix);
Configure::write('Croogo.Cache.defaultConfig', $cacheConfig);

/**
 * Settings
 */
Configure::config('settings', new DatabaseConfig());
Configure::load('settings', 'settings');

/**
 * Locale
 */
Configure::write('Config.language', Configure::read('Site.locale'));

I18n::config('croogo', function ($domain, $locale) {
    return new Package(
        'sprintf'
    );
});

/**
 * Timezone
 */
$timezone = Configure::read('Site.timezone');
if (!$timezone) {
    $timezone = 'UTC';
}
date_default_timezone_set($timezone);

/**
 * Assets
 */
if (Configure::check('Site.asset_timestamp')) {
    $timestamp = Configure::read('Site.asset_timestamp');
    Configure::write(
        'Asset.timestamp',
        is_numeric($timestamp) ? (bool)$timestamp : $timestamp
    );
    unset($timestamp);
}

// CakePHP Acl
Plugin::load(['Acl' => ['autoload' => true]]);

$croogoPath = Plugin::path('Croogo/Core');

/**
 * Extensions
 */
Plugin::load(['Croogo/Extensions' => [
    'autoload' => true,
    'bootstrap' => true,
    'routes' => true,
]]);
Configure::load('Croogo/Extensions.events');

/**
 * List of core plugins
 */
$corePlugins = [
    'Croogo/Settings', 'Croogo/Acl', 'Croogo/Blocks', 'Croogo/Comments', 'Croogo/Contacts', 'Croogo/Menus', 'Croogo/Meta',
    'Croogo/Nodes', 'Croogo/Taxonomy', 'Croogo/Users', 'Croogo/Wysiwyg', 'Croogo/Ckeditor',  'Croogo/Users', 'Croogo/Dashboards',
];
Configure::write('Core.corePlugins', $corePlugins);

/**
 * Plugins
 */
$aclPlugin = Configure::read('Site.acl_plugin');
$pluginBootstraps = Configure::read('Hook.bootstraps');
$plugins = array_filter(explode(',', $pluginBootstraps));

if (!in_array($aclPlugin, $plugins)) {
    $plugins = Hash::merge((array)$aclPlugin, $plugins);
}
foreach ($plugins as $plugin) {
    $pluginName = Inflector::camelize($plugin);
    $pluginPath = APP . 'Plugin' . DS . $pluginName;
    if ((!file_exists($pluginPath)) && (!strstr($plugin, 'Croogo/'))) {
        $pluginFound = false;
        foreach (App::path('Plugin') as $path) {
            if (is_dir($path . $pluginName)) {
                $pluginFound = true;
                break;
            }
        }
        if (!$pluginFound) {
            Log::error('Plugin not found during bootstrap: ' . $pluginName);
            continue;
        }
    }
    $option = [
        $pluginName => [
            'autoload' => true,
            'bootstrap' => true,
            'ignoreMissing' => true,
            'routes' => true,
        ]
    ];
    CroogoPlugin::load($option);
}
CroogoEventManager::loadListeners();
Croogo::dispatchEvent('Croogo.bootstrapComplete');
