<?php

namespace Croogo\Core\Config;

use Aura\Intl\Package;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Exception\MissingPluginException;
use Cake\I18n\I18n;
use Cake\I18n\MessagesFileLoader;
use Cake\Log\Log;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Routing\DispatcherFactory;

use Croogo\Core\Croogo;
use Croogo\Core\Event\EventManager;
use Croogo\Core\Plugin;
use Croogo\Settings\Configure\Engine\DatabaseConfig;

use function Croogo\Core\timerStart;
use function Croogo\Core\timerStop;

// Make sure that the Croogo event manager is the global one
EventManager::instance();

\Croogo\Core\time(function () {
    /**
     * Default Acl plugin.  Custom Acl plugin should override this value.
     */
    Configure::write('Site.acl_plugin', 'Croogo/Acl');

    /**
     * Default API Route Prefix. This can be overriden in settings.
     */
    Configure::write('Croogo.Api.path', 'api');

    /**
     * Admin theme
     */
    Configure::write('Site.admin_theme', 'Croogo/Core');

    /**
     * Cache configuration
     */
    $defaultCacheConfig = Cache::config('default');
    $defaultEngine = $defaultCacheConfig['className'];
    $defaultPrefix = Hash::get($defaultCacheConfig, 'prefix', 'cake_');
    $cacheConfig = [
        'duration' => '+1 hour',
        'path' => CACHE . 'queries' . DS,
        'className' => $defaultEngine,
        'prefix' => $defaultPrefix,
    ] + $defaultCacheConfig;
    Configure::write('Croogo.Cache.defaultEngine', $defaultEngine);
    Configure::write('Croogo.Cache.defaultPrefix', $defaultPrefix);
    Configure::write('Croogo.Cache.defaultConfig', $cacheConfig);

    $configured = Cache::configured();
    if (!in_array('cached_settings', $configured)) {
        Cache::config('cached_settings', array_merge(
            Configure::read('Croogo.Cache.defaultConfig'),
            ['groups' => ['settings']]
        ));
    }

    /**
     * Settings
     */
    Configure::config('settings', new DatabaseConfig());
    try {
        Configure::load('settings', 'settings');
    }
    catch (\Exception $e) {
        Log::error($e->getMessage());
        Log::error('You can ignore the above error during installation');
    }

    /**
     * Locale
     */
    $siteLocale = Configure::read('Site.locale');
    Configure::write('App.defaultLocale', $siteLocale);
    I18n::setLocale($siteLocale);

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

    /**
     * List of core plugins
     */
    $corePlugins = [
        'Croogo/Settings', 'Croogo/Acl', 'Croogo/Blocks', 'Croogo/Comments', 'Croogo/Contacts', 'Croogo/Menus', 'Croogo/Meta',
        'Croogo/Nodes', 'Croogo/Taxonomy', 'Croogo/Users', 'Croogo/Wysiwyg', 'Croogo/Ckeditor',  'Croogo/Dashboards',
    ];
    Configure::write('Core.corePlugins', $corePlugins);
}, 'Setting base configuration');

/**
 * Use old translation format for the croogo domain
 */
$siteLocale = Configure::read('App.defaultLocale');
I18n::config('croogo', function ($domain, $locale) {
    $loader = new MessagesFileLoader($domain, $locale, 'po');
    $package = new Package('sprintf', 'default');
    $localePackage = $loader();
    if ($localePackage) {
        $package->setMessages($localePackage->getMessages());
    }
    return $package;
});

/**
 * Timezone
 */
$timezone = Configure::read('Site.timezone');
if (!$timezone) {
    $timezone = 'UTC';
}
date_default_timezone_set($timezone);

\Croogo\Core\time(function () {
    /**
     * Load required plugins
     */
    if (!Plugin::loaded('Acl')) {
        Plugin::load('Acl', ['bootstrap' => true]);
    }
    if (!Plugin::loaded('BootstrapUI')) {
        Plugin::load('BootstrapUI');
    }

    /**
     * Extensions
     */
    Plugin::load(['Croogo/Extensions' => [
        'autoload' => true,
        'bootstrap' => true,
        'routes' => true,
        'events' => true
    ]]);
}, 'Loading dependencies');

/**
 * Plugins
 */
$aclPlugin = Configure::read('Site.acl_plugin');
$pluginBootstraps = Configure::read('Hook.bootstraps');
$plugins = array_filter(explode(',', $pluginBootstraps));

if (!in_array($aclPlugin, $plugins)) {
    $plugins = Hash::merge((array)$aclPlugin, $plugins);
}
$themes = [Configure::read('Site.theme'), Configure::read('Site.admin_theme')];
\Croogo\Core\time(function () use ($plugins, $themes) {
    $option = [
        'autoload' => true,
        'bootstrap' => true,
        'ignoreMissing' => true,
        'routes' => true,
        'events' => true
    ];
    foreach ($plugins as $plugin) {
        $plugin = Inflector::camelize($plugin);
        if (Plugin::loaded($plugin)) {
            continue;
        }

        try {
            Plugin::load($plugin, $option);
        } catch (MissingPluginException $e) {
            Log::error('Plugin not found during bootstrap: ' . $plugin);
            continue;
        }
    }


    foreach ($themes as $theme) {
        if ($theme && !Plugin::loaded($theme) && Plugin::available($theme)) {
            Plugin::load($theme, [
                'autoload' => true,
                'bootstrap' => true,
                'routes' => true,
                'events' => true,
                'ignoreMissing' => true
            ]);
        }
    }
}, 'plugins-loading-configured', 'Loading configured plugins: ' . implode(', ', $plugins + $themes));

DispatcherFactory::add('Croogo/Core.HomePage');

\Croogo\Core\time(function () {
    Plugin::events();

    EventManager::loadListeners();
}, 'Registering plugin listeners');


\Croogo\Core\time(function () {
    Croogo::dispatchEvent('Croogo.bootstrapComplete');
}, 'event-Croogo.bootstrapComplete', 'Event: Croogo.bootstrapComplete');
