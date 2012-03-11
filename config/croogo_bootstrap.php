<?php
/**
 * Locale
 */
	Configure::write('Config.language', 'eng');

/**
 * Admin theme
 */
	//Configure::write('Site.admin_theme', 'sample');

/**
 * Cache configuration
 */
	$cacheConfig = array(
		'duration' => '+1 hour',
		'path' => CACHE.'queries',
		'engine' => 'File',
	);

	// models
	Cache::config('setting_write_configuration', $cacheConfig);

	// components
	Cache::config('croogo_blocks', $cacheConfig);
	Cache::config('croogo_menus', $cacheConfig);
	Cache::config('croogo_nodes', $cacheConfig);
	Cache::config('croogo_types', $cacheConfig);
	Cache::config('croogo_vocabularies', $cacheConfig);

	// controllers
	Cache::config('nodes_view', $cacheConfig);
	Cache::config('nodes_promoted', $cacheConfig);
	Cache::config('nodes_term', $cacheConfig);
	Cache::config('nodes_index', $cacheConfig);
	Cache::config('contacts_view', $cacheConfig);

/**
 * Failed login attempts
 *
 * Default is 5 failed login attempts in every 5 minutes
 */
	Configure::write('User.failed_login_limit', 5);
	Configure::write('User.failed_login_duration', 300);
	Cache::config('users_login', array_merge($cacheConfig, array(
		'duration' => '+' . Configure::read('User.failed_login_duration') . ' seconds',
	)));

/**
 * Libraries
 */
	App::import('Vendor', 'Spyc/Spyc');

/**
 * Settings
 */
	if (file_exists(CONFIGS.'settings.yml')) {
		$settings = Spyc::YAMLLoad(file_get_contents(CONFIGS.'settings.yml'));
		foreach ($settings AS $settingKey => $settingValue) {
			Configure::write($settingKey, $settingValue);
		}
	}

/**
 * Plugins
 */
	$pluginBootstraps = Configure::read('Hook.bootstraps');
	if ($pluginBootstraps) {
		$plugins = explode(',', $pluginBootstraps);
		foreach ($plugins AS $plugin) {
			App::import('Plugin', Inflector::camelize($plugin) . 'Bootstrap');
		}
	}
