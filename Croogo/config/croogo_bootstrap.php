<?php

namespace Croogo\Croogo\Config;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Log\Log;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

use Croogo\Croogo\Croogo;
use Croogo\Croogo\Cache\CroogoCache;
use Croogo\Croogo\Configure\CroogoJsonReader;
use Croogo\Croogo\CroogoStatus;
use Croogo\Croogo\Event\CroogoEventManager;
use Croogo\Extensions\CroogoPlugin;

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
$defaultEngine = Configure::read('Cache.defaultEngine');
$defaultPrefix = Configure::read('Cache.defaultPrefix');
$cacheConfig = array(
	'duration' => '+1 hour',
	'path' => CACHE . 'queries' . DS,
	'className' => $defaultEngine,
	'prefix' => $defaultPrefix,
);
Configure::write('Cache.defaultConfig', $cacheConfig);

/**
 * Settings
 */
Configure::config('settings', new CroogoJsonReader());
if (file_exists(APP . 'Config' . DS . 'settings.json')) {
	Configure::load('settings', 'settings');
}

/**
 * Locale
 */
Configure::write('Config.language', Configure::read('Site.locale'));

/**
 * Assets
 */
if (Configure::check('Site.asset_timestamp')) {
	$timestamp = Configure::read('Site.asset_timestamp');
	Configure::write(
		'Asset.timestamp',
		is_numeric($timestamp) ? (bool) $timestamp : $timestamp
	);
	unset($timestamp);
}

// CakePHP Acl
Plugin::load(['Acl' => ['autoload' => true]]);

/**
 * Extensions
 */
Plugin::load(['Extensions' => [
	'autoload' => true,
	'bootstrap' => true,
	'routes' => true,
	'namespace' => 'Croogo\\Extensions\\',
	'classBase' => false,
]]);
Configure::load('Extensions.events');

/**
 * Setup custom paths
 */
$croogoPath = Plugin::path('Croogo');
App::build(array(
	'Console/Command' => array($croogoPath . 'Console' . DS . 'Command' . DS),
	'View' => array($croogoPath . 'View' . DS),
	'View/Helper' => array($croogoPath . 'View' . DS . 'Helper' . DS),
), App::APPEND);
if ($theme = Configure::read('Site.theme')) {
	App::build(array(
		'View/Helper' => array(App::themePath($theme) . 'Helper' . DS),
	));
}

/**
 * List of core plugins
 */
$corePlugins = [
	'Settings', 'Acl', 'Blocks', 'Comments', 'Contacts', 'Menus', 'Meta',
	'Nodes', 'Taxonomy', 'Users', 'Wysiwyg', 'Ckeditor',
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
	if (!file_exists($pluginPath)) {
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
	$option = array(
		$pluginName => array(
			'autoload' => true,
			'bootstrap' => true,
			'ignoreMissing' => true,
			'routes' => true,
		)
	);
	if (in_array($pluginName, $corePlugins)) {
		$option[$pluginName]['namespace'] = 'Croogo\\' . $pluginName . '\\';
		$option[$pluginName]['classBase'] = false;
	}
	CroogoPlugin::load($option);
}
CroogoEventManager::loadListeners();
Croogo::dispatchEvent('Croogo.bootstrapComplete');
