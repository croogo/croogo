<?php
/**
 * Default Acl plugin.  Custom Acl plugin should override this value.
 */
Configure::write('Site.acl_plugin', 'Acl');

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
 * Setup custom paths
 */
$croogoPath = CakePlugin::path('Croogo');
App::build(array(
	'Console/Command' => array($croogoPath . 'Console' . DS . 'Command' . DS),
	'Controller' => array($croogoPath . 'Controller' . DS),
	'Controller/Component' => array($croogoPath . 'Controller' . DS . 'Component' . DS),
	'Lib' => array($croogoPath . 'Lib' . DS),
	'Model' => array($croogoPath . 'Model' . DS),
	'Model/Behavior' => array($croogoPath . 'Model' . DS . 'Behavior' . DS),
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
		CakeLog::error('Plugin not found during bootstrap: ' . $pluginName);
		continue;
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
