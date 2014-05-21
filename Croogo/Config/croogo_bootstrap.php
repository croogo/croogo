<?php

namespace Croogo\Croogo\Config;
App::uses('CroogoStatus', 'Croogo.Lib');

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
App::uses('CroogoCache', 'Croogo.Cache');
$defaultEngine = Configure::read('Cache.defaultEngine');
$defaultPrefix = Configure::read('Cache.defaultPrefix');
$cacheConfig = array(
	'duration' => '+1 hour',
	'path' => CACHE . 'queries' . DS,
	'engine' => $defaultEngine,
	'prefix' => $defaultPrefix,
);
Configure::write('Cache.defaultConfig', $cacheConfig);

/**
 * Settings
 */
App::uses('CroogoJsonReader', 'Croogo.Configure');
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

/**
 * Extensions
 */
CakePlugin::load(array('Extensions'), array('bootstrap' => true, 'routes' => true));
Configure::load('Extensions.events');
App::uses('CroogoPlugin', 'Extensions.Lib');

/**
 * Setup custom paths
 */
$croogoPath = CakePlugin::path('Croogo');
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
Configure::write('Core.corePlugins', array(
	'Settings', 'Acl', 'Blocks', 'Comments', 'Contacts', 'Menus', 'Meta',
	'Nodes', 'Taxonomy', 'Users',
));

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
			CakeLog::error('Plugin not found during bootstrap: ' . $pluginName);
			continue;
		}
	}
	$option = array(
		$pluginName => array(
			'bootstrap' => true,
			'routes' => true,
			'ignoreMissing' => true,
		)
	);
	CroogoPlugin::load($option);
}
CroogoEventManager::loadListeners();
Croogo::dispatchEvent('Croogo.bootstrapComplete');
