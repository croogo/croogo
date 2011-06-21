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

    // permissions
    Cache::config('permissions', $cacheConfig);

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
    if (file_exists(APP . 'Config' . DS.'settings.yml')) {
        $settings = Spyc::YAMLLoad(file_get_contents(APP . 'Config' . DS.'settings.yml'));
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
            $pluginName = Inflector::camelize($plugin);
            if (!file_exists(APP . 'Plugin' .DS. $pluginName)) {
                CakeLog::write(LOG_ERR, 'Plugin not found during bootstrap: ' . $pluginName);
                continue;
            }
            $bootstrapFile = APP . 'Plugin' .DS. $pluginName .DS. 'Config' .DS. 'bootstrap.php';
            $bootstrap = file_exists($bootstrapFile);
            $routesFile = APP . 'Plugin' .DS. $pluginName .DS. 'Config' .DS. 'routes.php';
            $routes = file_exists($routesFile);
            $option = array(
                $pluginName => array(
                    'bootstrap' => $bootstrap,
                    'routes' => $routes,
                    )
                );
            CakePlugin::load($option);
        }
    }
?>