<?php

namespace Croogo\Core;

use App\Controller\AppController;
use Aura\Intl\Package;
use Cake\Cache\Cache;
use Cake\Core\App;
use Cake\Core\BasePlugin;
use Cake\Core\ClassLoader;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Core\Exception\MissingPluginException;
use Cake\Core\Plugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Database\SchemaCache;
use Cake\Database\Type;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use Cake\Filesystem\Folder;
use Cake\I18n\I18n;
use Cake\I18n\MessagesFileLoader;
use Cake\Log\Log;
use Cake\Log\LogTrait;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Croogo\Core\Event\EventManager;
use Croogo\Settings\Configure\Engine\DatabaseConfig;
use InvalidArgumentException;
use Migrations\Migrations;

/**
 * PluginManager utility class
 *
 * @category Component
 * @package  Croogo.Extensions.Lib
 * @version  1.4
 * @since    1.4
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class PluginManager extends Plugin
{

    use LogTrait;

    /**
     * List of migration errors
     * Updated in case of errors when running migrations
     *
     * @var array
     */
    public $migrationErrors = [];

    /**
     * PluginActivation class
     *
     * @var object
     */
    protected $_PluginActivation = null;

    /**
     * MigrationVersion class
     *
     * @var \Migrations\Migrations
     */
    protected $_Migrations = null;

    /**
     * Core plugins
     *
     * Typically these plugins must be active and should not be deactivated
     *
     * @var array
     * @access public
     */
    public static $corePlugins = [
        'Croogo/Acl',
        'Croogo/Core',
        'Croogo/Extensions',
        'Croogo/Install',
        'Croogo/Settings',
        'Migrations',
        'Search',
    ];

    /**
     * Bundled plugins providing core functionalities but could be deactivated
     *
     * @var array
     * @access public
     */
    public static $bundledPlugins = [
        'Croogo/Blocks',
        'Croogo/Contacts',
        'Croogo/Dashboards',
        'Croogo/FileManager',
        'Croogo/Meta',
        'Croogo/Menus',
        'Croogo/Nodes',
        'Croogo/Taxonomy',
        'Croogo/Users',
    ];

    /**
     * __construct
     */
    public function __construct($migrations = null)
    {
        if (!is_null($migrations)) {
            $this->_Migrations = $migrations;
        }
    }

    /**
     * Get instance
     */
    public static function instance()
    {
        static $self = null;
        if ($self === null) {
            $self = new static();
        }

        return $self;
    }

    /**
     * AppController setter
     *
     * @return void
     */
    public function setController(AppController $controller)
    {
        $this->_Controller = $controller;
    }

    /**
     * Get plugin aliases (folder names)
     *
     * @return array
     */
    public function getPlugins($type = 'plugin')
    {
        $plugins = [];
        $this->folder = new Folder;
        $registered = Configure::read('plugins');
        $pluginPaths = Hash::merge(App::path('Plugin'), $registered);
        unset($pluginPaths['Croogo']); //Otherwise we get croogo plugins twice!
        foreach ($pluginPaths as $pluginName => $pluginPath) {
            $this->folder->path = $pluginPath;
            if (!file_exists($this->folder->path)) {
                continue;
            }
            if ((
                    $type === 'plugin' && $this->_isCroogoPlugin($pluginPath)
                ) || (
                    $type === 'theme' && $this->_isCroogoTheme($pluginPath)
                )
            ) {
                $plugins[$pluginName] = $pluginPath;
                continue;
            }

            $pluginFolders = $this->folder->read();
            foreach ($pluginFolders[0] as $pluginFolder) {
                if (substr($pluginFolder, 0, 1) != '.') {
                    if ($type === 'plugin' && !$this->_isCroogoPlugin($pluginPath, $pluginFolder)) {
                        continue;
                    }
                    if ($type === 'theme' && !$this->_isCroogoTheme($pluginPath, $pluginFolder)) {
                        continue;
                    }

                    $pluginName = array_search($pluginPath . $pluginFolder, $registered);
                    if ($pluginName) {
                        $plugins[$pluginName] = $pluginPath . $pluginFolder;
                    } else {
                        $plugins[$pluginFolder] = $pluginPath . $pluginFolder;
                    }
                }
            }
        }

        return $plugins;
    }

    /**
     * Checks wether $pluginDir/$path is a Croogo theme
     *
     * @param string $pluginDir plugin directory
     * @param string $path plugin alias
     * @return bool true if path is a Croogo plugin
     */
    protected function _isCroogoTheme($pluginDir, $path = '')
    {
        $dir = $pluginDir . $path . DS;
        $themeConfigs = [
            'config' . DS . 'theme.json',
            'webroot' . DS . 'theme.json',
        ];

        $composerFile = $dir . 'composer.json';
        if (file_exists($composerFile)) {
            $json = json_decode(file_get_contents($composerFile));

            if (property_exists($json, 'type') && $json->type === 'croogo-theme') {
                return true;
            }
        }

        foreach ($themeConfigs as $themeManifestFile) {
            if (!file_exists($dir . $themeManifestFile)) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * Checks wether $pluginDir/$path is a Croogo plugin
     *
     * @param string $pluginDir plugin directory
     * @param string $path plugin alias
     * @return bool true if path is a Croogo plugin
     */
    protected function _isCroogoPlugin($pluginDir, $path = '')
    {
        $dir = $pluginDir . $path . DS;
        $composerFile = $dir . 'composer.json';
        if (file_exists($dir . 'config' . DS . 'plugin.json')) {
            return true;
        }
        if (file_exists($composerFile) && !$this->_isCroogoTheme($pluginDir, $path)) {
            $pluginData = json_decode(file_get_contents($composerFile), true);
            if (isset($pluginData['require']['croogo/core']) ||
                isset($pluginData['require']['croogo/croogo'])
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $pluginDir
     * @param $path
     *
     * @return bool
     */
    protected function _pluginName($pluginDir, $path)
    {
        $pluginPath = $pluginDir . $path . DS;
        $manifestFile = $pluginPath . DS . 'config' . DS . 'plugin.json';

        if (file_exists($manifestFile)) {
            $manifestData = json_decode(file_get_contents($manifestFile), true);
            if (isset($manifestData['name'])) {
                return $manifestData['name'];
            }
        }
        $composerFile = $pluginPath . $alias . DS . 'composer.json';

        if (file_exists($composerFile)) {
            $composerData = json_decode(file_get_contents($composerFile), true);
            if (isset($composerData['name'])) {
                return $composerData['name'];
            }
        }

        return false;
    }

    /**
     * Checks whether $plugin is builtin
     *
     * @param string $plugin plugin alias
     * @return bool true if $plugin is builtin
     */
    protected function _isBuiltin($plugin)
    {
        return in_array($plugin, static::$bundledPlugins) || in_array($plugin, static::$corePlugins);
    }

    /**
     * @param $alias
     * @param $pluginPath
     * @param bool $ignoreMigration
     *
     * @return array|bool
     */
    protected function _loadData($alias, $pluginPath, $ignoreMigration = true)
    {
        $active = $this->isActive($alias);
        $manifestFile = $pluginPath . DS . 'config' . DS . 'plugin.json';
        $hasManifest = file_exists($manifestFile);
        $composerFile = $pluginPath . DS . 'composer.json';
        $hasComposer = file_exists($composerFile);
        if ($hasManifest || $hasComposer) {
            $pluginData = [
                'name' => $alias,
                'needMigration' => false,
                'active' => $active,
            ];

            if ($hasManifest) {
                $manifestData = json_decode(file_get_contents($manifestFile), true);
                if (empty($manifestData)) {
                    $this->log($alias . 'plugin.json exists but cannot be decoded.');

                    return $pluginData;
                }
                $pluginData = array_merge($manifestData, $pluginData);
            }

            if ($hasComposer) {
                $composerData = json_decode(file_get_contents($composerFile), true);
                $type = isset($composerData['type']) ? $composerData['type'] : null;
                $isCroogoPlugin = isset($composerData['require']['Croogo/Core']) || $type == 'croogo-plugin';

                if ($isCroogoPlugin) {
                    if (isset($composerData['name'])) {
                        $composerData['vendor'] = $composerData['name'];
                        unset($composerData['name']);
                    }
                    $pluginData = Hash::merge($pluginData, $composerData);
                }
            }

            if (!$ignoreMigration) {
                $pluginData['needMigration'] = $this->needMigration($alias, $active);
            }

            return $pluginData;
        } elseif ($this->_isBuiltin($alias)) {
            if (!$ignoreMigration && $this->needMigration($alias, $active)) {
                $pluginData = [
                    'name' => $alias,
                    'description' => "Croogo $alias plugin",
                    'active' => true,
                    'needMigration' => true,
                ];

                return $pluginData;
            }
        }

        return false;
    }

    /**
     * Get the content of plugin.json file of a plugin
     *
     * @param string $plugin name of plugin
     * @return array|bool array of plugin manifest or boolean false
     */
    public function getData($plugin = null, $ignoreMigrations = true)
    {
        if (!static::available($plugin)) {
            return false;
        }

        return $this->_loadData($plugin, static::path($plugin), $ignoreMigrations);
    }

    /**
     * Get a list of plugins available with all available meta data including migration status.
     * Plugin without metadata are excluded.
     *
     * @return array array of plugins, listed according to bootstrap order
     */
    public function plugins($ignoreMigrations = true)
    {
        $pluginAliases = $this->getPlugins();
        $allPlugins = [];
        foreach ($pluginAliases as $pluginAlias => $pluginPath) {
            $pluginData = $this->getData($pluginAlias, $ignoreMigrations);
            if (!$pluginData) {
                $pluginData = $this->_loadData($pluginAlias, $pluginPath, $ignoreMigrations);
            }
            $allPlugins[$pluginAlias] = $pluginData;
        }

        $activePlugins = [];
        $bootstraps = explode(',', Configure::read('Hook.bootstraps'));
        foreach ($bootstraps as $pluginAlias) {
            if ($pluginData = $this->getData($pluginAlias, $ignoreMigrations)) {
                $activePlugins[$pluginAlias] = $pluginData;
            }
        }

        $plugins = [];
        foreach ($activePlugins as $plugin => $pluginData) {
            $plugins[$plugin] = $pluginData;
        }
        $plugins = Hash::merge($plugins, $allPlugins);

        return $plugins;
    }

    /**
     * Check if plugin is dependent on any other plugin.
     * If yes, check if that plugin is available in plugins directory.
     *
     * @param string $plugin plugin alias
     * @return bool
     */
    public function checkDependency($plugin = null)
    {
        $dependencies = $this->getDependencies($plugin);
        $pluginPaths = App::path('plugins');
        foreach ($dependencies as $p) {
            $check = false;
            foreach ($pluginPaths as $pluginPath) {
                if (is_dir($pluginPath . $p)) {
                    $check = true;
                }
            }
            if (!$check) {
                return false;
            }
        }

        return true;
    }

    /**
     * getDependencies
     *
     * @param string $plugin plugin alias (underscrored)
     * @return array list of plugin that $plugin depends on
     */
    public function getDependencies($plugin)
    {
        $pluginData = $this->getData($plugin);
        if (!isset($pluginData['dependencies']['plugins'])) {
            $pluginData['dependencies']['plugins'] = [];
        }
        $dependencies = [];
        foreach ($pluginData['dependencies']['plugins'] as $i => $plugin) {
            $dependencies[] = Inflector::camelize($plugin);
        }

        return $dependencies;
    }

    /**
     * Check if plugin is dependent on any other plugin.
     * If yes, check if that plugin is available in plugins directory.
     *
     * @param string $plugin plugin alias (underscrored)
     * @return bool
     */
    public function checkPluginDependency($plugin = null)
    {
        return $this->checkDependency($plugin);
    }

    /**
     * Check if plugin is active
     *
     * @param string $plugin Plugin name (underscored)
     * @return bool
     */
    public static function isActive($plugin)
    {
        $configureKeys = [
            'Hook.bootstraps',
        ];

        $plugin = [Inflector::underscore($plugin), Inflector::camelize($plugin)];

        foreach ($configureKeys as $configureKey) {
            $hooks = explode(',', Configure::read($configureKey));
            foreach ($hooks as $hook) {
                if (in_array($hook, $plugin)) {
                    return true;
                }
            }
        }

        // check for manually loaded plugins
        foreach ($plugin as $item) {
            if ($loaded = Plugin::isLoaded($item)) {
                return $loaded;
            }
        }

        return false;
    }

    /**
     * Check if a plugin need a database migration
     *
     * @param string $plugin Plugin name or 'app'
     * @param string $isActive If the plugin is active
     * @return bool
     */
    public function needMigration($plugin, $isActive)
    {
        if (!$isActive) {
            return false;
        }
        if (($plugin !== 'app') && (!static::available($plugin))) {
            return false;
        }

        $options = [
            'connection' => static::migrationConnectionName()
        ];
        if ($plugin !== 'app') {
            $options['plugin'] = $plugin;
        }
        $status = $this->_getMigrations()->status($options);
        if ($status) {
            return Hash::check($status, '{n}[status=down]');
        }

        return false;
    }

    /**
     * Migrate a plugin
     *
     * @param string $plugin Plugin name
     * @return bool Success of the migration
     */
    public function migrate($plugin)
    {
        if (($plugin !== 'app') && (!static::available($plugin))) {
            return false;
        }
        if (!$this->needMigration($plugin, true)) {
            return true;
        }

        $connectionName = static::migrationConnectionName();
        $options = [
            'connection' => $connectionName,
        ];
        if ($plugin !== 'app') {
            $options['plugin'] = $plugin;
        }

        try {
            $migrated = $this->_getMigrations()
                ->migrate($options);
            $connection = ConnectionManager::get($connectionName);
            $schemaCache = new SchemaCache($connection);
            $schemaCache->clear();
            return $migrated;
        } catch (\Exception $e) {
            $this->migrationErrors[] = $e->getMessage();
        }
    }

    /**
     * @param $plugin
     *
     * @return bool
     */
    public function seed($plugin)
    {
        $options = [
            'connection' => static::migrationConnectionName()
        ];
        if ($plugin !== 'app') {
            $options['plugin'] = $plugin;
        }

        return $this->_getMigrations()
            ->seed($options);
    }

    /**
     * @param $plugin
     *
     * @return bool
     */
    public function unmigrate($plugin)
    {
        if (($plugin !== 'app') && (!static::available($plugin))) {
            return false;
        }

        $options = [
            'connection' => static::migrationConnectionName(),
            'target' => 0
        ];
        if ($plugin !== 'app') {
            $options['plugin'] = $plugin;
        }

        return $this->_getMigrations()
            ->rollback($options);
    }

    /**
     * @return \Migrations\Migrations
     */
    protected function _getMigrations()
    {
        $this->_Migrations = new Migrations();

        return $this->_Migrations;
    }

    /**
     * Loads plugin's bootstrap.php file
     *
     * @param string $plugin Plugin name
     * @return void
     */
    public function addBootstrap($plugin)
    {
        $hookBootstraps = Configure::read('Hook.bootstraps');
        if (!$hookBootstraps) {
            $plugins = [];
        } else {
            $plugins = explode(',', $hookBootstraps);
            $names = [Inflector::underscore($plugin), Inflector::camelize($plugin)];
            if ($intersect = array_intersect($names, $plugins)) {
                $plugin = current($intersect);
            }
        }

        if (array_search($plugin, $plugins) !== false) {
            $plugins = (array)$hookBootstraps;
        } else {
            $plugins[] = $plugin;
        }
        $this->_saveBootstraps($plugins);
    }

    /**
     * Plugin name will be removed from Hook.bootstraps
     *
     * @param string $plugin Plugin name
     * @return void
     */
    public function removeBootstrap($plugin)
    {
        $hookBootstraps = Configure::read('Hook.bootstraps');
        if (!$hookBootstraps) {
            return;
        }

        $plugins = explode(',', $hookBootstraps);
        $names = [Inflector::underscore($plugin), Inflector::camelize($plugin)];
        if ($intersect = array_intersect($names, $plugins)) {
            $plugin = current($intersect);
            $k = array_search($plugin, $plugins);
            unset($plugins[$k]);
        }

        $this->_saveBootstraps($plugins);
    }

    /**
     * Get PluginActivation class
     *
     * @param string $plugin
     * @return object
     */
    public function getActivator($plugin = null)
    {
        $plugin = Inflector::camelize($plugin);
        if (!isset($this->_PluginActivation)) {
            $className = 'PluginActivation';

            $registered = Configure::read('plugins');
            $pluginPaths = Hash::merge(App::path('Plugin'), $registered);
            unset($pluginPaths['Croogo']); //Otherwise we get croogo plugins twice!

            if (isset($pluginPaths[$plugin])) {
                $activationFile = $pluginPaths[$plugin] . 'config/PluginActivation.php';
                $configFile = $pluginPaths[$plugin] . 'config' . DS . $className . '.php';
                if (
                    (file_exists($configFile) && include $configFile) ||
                    (file_exists($activationFile) && include $activationFile)
                ) {
                    $fqcn = App::className($plugin . '.' . $className, 'Config');
                    if (!$fqcn) {
                        $this->log(sprintf(
                            'Unable to load PluginActivation class. Expected class name is %s\\Config\\PluginActivation',
                            str_replace('/', '\\', $plugin),
                            LOG_CRIT
                        ));

                        return null;
                    }
                    $this->_PluginActivation = new $fqcn;

                    return $this->_PluginActivation;
                }
            }
            foreach ($pluginPaths as $path) {
                $configFile = $path . DS . $plugin . DS . 'config' . DS . $className . '.php';
                if (file_exists($configFile) && include $configFile) {
                    $fqcn = App::className($plugin . '.' . $className, 'Config');
                    if (!$fqcn) {
                        $this->log(sprintf(
                            'Unable to load PluginActivation class. Expected class name is %s\\Config\\PluginActivation',
                            str_replace('/', '\\', $plugin),
                            LOG_CRIT
                        ));

                        return null;
                    }
                    $this->_PluginActivation = new $fqcn;

                    return $this->_PluginActivation;
                }
            }
        }

        return $this->_PluginActivation;
    }

    /**
     * Activate plugin
     *
     * @param string $plugin Plugin name
     * @return bool true when successful, false or error message when failed
     */
    public function activate($plugin, $dependencyList = [])
    {
        if (Plugin::isLoaded($plugin)) {
            return true;
        }
        $pluginActivation = $this->getActivator($plugin);
        if (!isset($pluginActivation) ||
            (isset($pluginActivation) &&
                method_exists($pluginActivation, 'beforeActivation') &&
                $pluginActivation->beforeActivation($this->_Controller))
        ) {
            $pluginData = $this->getData($plugin);
            $missingPlugins = [];
            if (!empty($pluginData['dependencies']['plugins'])) {
                foreach ($pluginData['dependencies']['plugins'] as $requiredPlugin) {
                    $requiredPlugin = ucfirst($requiredPlugin);
                    if (!Plugin::isLoaded($requiredPlugin)) {
                        $dependencyList[] = $plugin;
                        if ($this->activate($requiredPlugin, $dependencyList) !== true) {
                            $missingPlugins[] = $requiredPlugin;
                        }
                    }
                }
            }
            if (!empty($missingPlugins)) {
                return __dn(
                    'croogo',
                    'Plugin "%2$s" requires the "%3$s" plugin to be installed.',
                    'Plugin "%2$s" requires the %3$s plugins to be installed.',
                    count($missingPlugins),
                    $plugin,
                    Text::toList($missingPlugins)
                );
            }

            try {
                static::load($plugin);
            } catch (MissingPluginException $e) {
                return __d('croogo', 'Plugin "%s" could not be actived.', $plugin);
            }

            $this->addBootstrap($plugin);
            if (isset($pluginActivation) && method_exists($pluginActivation, 'onActivation')) {
                $pluginActivation->onActivation($this->_Controller);
            }

            Cache::clear(false, 'croogo_menus');
            Cache::delete('file_map', '_cake_core_');

            return true;
        }
    }

    /**
     * Return a list of plugins that uses $plugin
     *
     * @return array|bool Boolean false or Array of plugin names
     */
    public function usedBy($plugin)
    {
        $deps = Configure::read('pluginDeps');
        if (empty($deps['usedBy'][$plugin])) {
            return false;
        }
        $usedBy = array_filter($deps['usedBy'][$plugin], ['Croogo\\Core\\Plugin', 'loaded']);
        if (!empty($usedBy)) {
            return $usedBy;
        }

        return false;
    }

    /**
     * Deactivate plugin
     *
     * @param string $plugin Plugin name
     * @return bool true when successful, false or error message when failed
     */
    public function deactivate($plugin)
    {
        if (!Plugin::isLoaded($plugin)) {
            return __d('croogo', 'Plugin "%s" is not active.', $plugin);
        }
        $pluginActivation = $this->getActivator($plugin);
        if (!isset($pluginActivation) ||
            (isset($pluginActivation) &&
                method_exists($pluginActivation, 'beforeDeactivation') &&
                $pluginActivation->beforeDeactivation($this->_Controller))
        ) {
            $this->removeBootstrap($plugin);
            if (isset($pluginActivation) && method_exists($pluginActivation, 'onDeactivation')) {
                $pluginActivation->onDeactivation($this->_Controller);
            }
            static::unload($plugin);

            Cache::clear(false, 'croogo_menus');
            Cache::delete('file_map', '_cake_core_');

            return true;
        } else {
            return __d('croogo', 'Plugin could not be deactivated. Please, try again.');
        }
    }

    /**
     * Cache plugin dependency list
     */
    public static function cacheDependencies()
    {
        $pluginDeps = Cache::read('pluginDeps', 'cached_settings');
        if (!$pluginDeps) {
            $self = self::instance();
            $plugins = Plugin::loaded();
            $dependencies = $usedBy = [];
            foreach ($plugins as $plugin) {
                $dependencies[$plugin] = $self->getDependencies($plugin);
                foreach ($dependencies[$plugin] as $dependent) {
                    $usedBy[$dependent][] = $plugin;
                }
            }
            $pluginDeps = compact('dependencies', 'usedBy');
            Cache::write('pluginDeps', $pluginDeps, 'cached_settings');
        }
        Configure::write('pluginDeps', $pluginDeps);
    }

    /**
     * @param string $name
     *
     * @return bool|mixed
     */
    public static function bootstrap($name)
    {
        $plugin = static::getCollection()->get($name);
        if (!$plugin->isEnabled('bootstrap')) {
            return false;
        }
        // Disable bootstrapping for this plugin as it will have
        // been bootstrapped.
        $plugin->disable('bootstrap');

        return static::_includeFile(
            $plugin->getConfigPath() . 'bootstrap.php',
            true
        );
    }

    /**
     * Loads a plugin and optionally loads bootstrapping and routing files.
     *
     * This method is identical to Plugin::load() with extra functionality
     * that loads event configuration when Plugin/Config/events.php is present.
     *
     * @param mixed $plugin name of plugin, or array of plugin and its config
     * @return void
     * @see Plugin::load()
     */
    public static function load($plugin, array $config = [])
    {
        if (is_array($plugin)) {
            foreach ($plugin as $name => $conf) {
                list($name, $conf) = is_numeric($name) ? [$conf, $config] : [$name, $conf];
                static::load($name, $conf);
            }

            return;
        }

        $config += [
            'autoload' => false,
            'bootstrap' => false,
            'routes' => false,
            'console' => true,
            'classBase' => 'src',
            'ignoreMissing' => false,
            'events' => false,
            'name' => $plugin
        ];

        if (!isset($config['path'])) {
            $config['path'] = static::getCollection()->findPath($plugin);
        }

        $config['classPath'] = $config['path'] . $config['classBase'] . DIRECTORY_SEPARATOR;
        if (!isset($config['configPath'])) {
            $config['configPath'] = $config['path'] . 'config' . DIRECTORY_SEPARATOR;
        }
        $pluginClass = str_replace('/', '\\', $plugin) . '\\Plugin';
        if (class_exists($pluginClass)) {
            $instance = new $pluginClass($config);
        } else {
            // Use stub plugin as this method will be removed long term.
            $instance = new BasePlugin($config);
        }
        static::getCollection()->add($instance);

        if ($config['autoload'] === true) {
            if (empty(static::$_loader)) {
                static::$_loader = new ClassLoader();
                static::$_loader->register();
            }
            static::$_loader->addNamespace(
                str_replace('/', '\\', $plugin),
                $config['path'] . $config['classBase'] . DIRECTORY_SEPARATOR
            );
            static::$_loader->addNamespace(
                str_replace('/', '\\', $plugin) . '\Test',
                $config['path'] . 'tests' . DIRECTORY_SEPARATOR
            );
        }

        if ($config['bootstrap'] === true) {
            static::bootstrap($plugin);
        }

        if (in_array('cached_settings', Cache::configured())) {
            Cache::delete('EventHandlers', 'cached_settings');
        }
    }

    /**
     * Forgets a loaded plugin or all of them if first parameter is null
     *
     * This method is identical to Plugin::load() with extra functionality
     * that unregister event listeners when a plugin in unloaded.
     *
     * @param string $plugin name of the plugin to forget
     * @return void
     */
    public static function unload($plugin = null)
    {
        if (is_array($plugin)) {
            foreach ($plugin as $name) {
                if ($name === null) {
                    static::getCollection()->clear();
                } else {
                    static::getCollection()->remove($plugin);
                }
            }

            return;
        }

        $eventManager = EventManager::instance();
        if ($eventManager instanceof EventManager) {
            if ($plugin == null) {
                $activePlugins = static::loaded();
                foreach ($activePlugins as $activePlugin) {
                    $eventManager->detachPluginSubscribers($activePlugin);
                }
            } else {
                $eventManager->detachPluginSubscribers($plugin);
            }
        }
        Cache::delete('EventHandlers', 'cached_settings');
    }

    /**
     * Delete plugin
     *
     * @param string $plugin Plugin name
     * @return bool true when successful, false or array of error messages when failed
     * @throws InvalidArgumentException
     */
    public function delete($plugin)
    {
        if (empty($plugin)) {
            throw new InvalidArgumentException(__d('croogo', 'Invalid plugin'));
        }
        $pluginPath = ROOT . DS . 'plugins' . DS . $plugin;
        if (is_link($pluginPath)) {
            return unlink($pluginPath);
        }
        $folder = new Folder();
        $result = $folder->delete($pluginPath);
        if ($result !== true) {
            return $folder->errors();
        }

        return true;
    }

    /**
     * Move plugin up or down in the bootstrap order
     *
     * @param string $dir valid values 'up' or 'down'
     * @param string $plugin plugin alias
     * @param array $bootstraps current bootstrap order
     * @return array|string array when successful, string contains error message
     */
    protected function _move($dir, $plugin, $bootstraps)
    {
        $index = array_search($plugin, $bootstraps);

        if ($dir === 'up') {
            if ($index) {
                $swap = $bootstraps[$index - 1];
            }
            if ($index == 0 || $this->_isBuiltin($swap)) {
                return __d('croogo', '%s is already at the first position', $plugin);
            }
            $before = array_slice($bootstraps, 0, $index - 1);
            $after = array_slice($bootstraps, $index + 1);
            $dependencies = $this->getDependencies($plugin);
            if (in_array($swap, $dependencies)) {
                return __d('croogo', 'Plugin %s depends on %s', $plugin, $swap);
            }
            $reordered = array_merge($before, (array)$plugin, (array)$swap);
        } elseif ($dir === 'down') {
            if ($index >= count($bootstraps) - 1) {
                return __d('croogo', '%s is already at the last position', $plugin);
            }
            $swap = $bootstraps[$index + 1];
            $before = array_slice($bootstraps, 0, $index);
            $after = array_slice($bootstraps, $index + 2);
            $dependencies = $this->getDependencies($swap);
            if (in_array($plugin, $dependencies)) {
                return __d('croogo', 'Plugin %s depends on %s', $swap, $plugin);
            }
            $reordered = array_merge($before, (array)$swap, (array)$plugin);
        } else {
            return __d('croogo', 'Invalid direction');
        }
        $reordered = array_merge($reordered, $after);

        if ($this->_isBuiltin($swap)) {
            return __d('croogo', 'Plugin %s cannot be reordered', $swap);
        }

        return $reordered;
    }

    /**
     * Write Hook.bootstraps settings to database and json file
     *
     * @param array $bootstraps array of plugin aliases
     * @return bool
     * @throws Exception
     */
    protected function _saveBootstraps($bootstraps)
    {
        static $Setting = null;
        if (empty($Setting)) {
            if (!Configure::read('Croogo.installed')) {
                throw new Exception('Unable to save Hook.bootstraps when Croogo is not fully installed');
            }
            $Settings = TableRegistry::get('Croogo/Settings.Settings');
        }

        return $Settings->write('Hook.bootstraps', implode(',', $bootstraps));
    }

    /**
     * Move plugin in the bootstrap order
     *
     * @param string $dir direction 'up' or 'down'
     * @param string $plugin plugin alias
     * @param array $bootstraps array of plugin aliases
     * @return string|bool true when successful, string contains error message
     */
    public function move($dir, $plugin, $bootstraps = null)
    {
        if (empty($bootstraps)) {
            $bootstraps = explode(',', Configure::read('Hook.bootstraps'));
        }
        $reordered = $this->_move(strtolower($dir), $plugin, $bootstraps);
        if (is_string($reordered)) {
            return $reordered;
        }

        return $this->_saveBootstraps($reordered);
    }

    /**
     * Returns the filesystem path for a plugin
     *
     * @param string $plugin name of the plugin in CamelCase format
     * @return string path to the plugin folder
     * @throws \Cake\Core\Exception\MissingPluginException if the folder for plugin was not found or plugin has not
     *     been loaded
     */
    public static function path($plugin)
    {
        if (strstr($plugin, 'Croogo/')) {
            return realpath(parent::path('Croogo/Core') . '..' . DS . substr($plugin, 7) . DS) . DS;
        }

        $path = Configure::read('plugins.' . $plugin);
        if ($path) {
            return $path;
        }

        $paths = App::path('Plugin');
        $pluginPath = str_replace('/', DIRECTORY_SEPARATOR, $plugin);
        foreach ($paths as $path) {
            if (!is_dir($path . $pluginPath)) {
                continue;
            }

            return $path . $pluginPath;
        }

        return parent::path($plugin);
    }

    /**
     * Loads the events file for a plugin, or all plugins configured to load their respective events file
     *
     * @param string|null $plugin name of the plugin, if null will operate on all plugins having enabled the
     * loading of events files
     * @return bool
     */
    public static function events($plugin = null)
    {
        if ($plugin === null) {
            foreach (Plugin::loaded() as $p) {
                static::events($p);
            }

            return true;
        }
        $instance = static::$plugins->get($plugin);
        //debug($instance);
        //if ((!isset($instance->events)) || ($instance->events === false)) {
        //    return false;
        //}

        if (!file_exists($instance->getConfigPath() . 'events.php')) {
            return false;
        }

        return Configure::load($plugin . '.events');
    }

    /**
     * Check whether a plugin can be loaded without the having to specify the path as an option.
     *
     * @param string $plugin name of the plugin
     * @return bool
     */
    public static function available($plugin)
    {
        if (Plugin::isLoaded($plugin)) {
            return true;
        }

        try {
            if (!static::path($plugin)) {
                return false;
            }

            return true;
        } catch (MissingPluginException $exception) {
        }

        return false;
    }

    /**
     * @return string
     */
    protected static function migrationConnectionName()
    {
        if (static::getConnectionConfiguration('default', false)) {
            return 'default';
        }

        return static::getConnectionConfiguration('default')['name'];
    }

    /**
     * @param $connectionName
     * @param bool $useAliases
     *
     * @return array|bool
     */
    protected static function getConnectionConfiguration($connectionName, $useAliases = true)
    {
        try {
            return ConnectionManager::get($connectionName, $useAliases)->config();
        } catch (MissingDatasourceConfigException $exception) {
        }

        return false;
    }

    /**
     * @param PluginApplicationInterface $app
     * @return void
     */
    public static function setup(PluginApplicationInterface $app)
    {
        $dbConfigExists = false;

        if (file_exists(ROOT . DS . 'config' . DS . 'database.php')) {
            Configure::load('database', 'default');
            ConnectionManager::drop('default');
            ConnectionManager::config(Configure::consume('Datasources'));
        }

        try {
            $defaultConnection = ConnectionManager::get('default');
            $dbConfigExists = $defaultConnection->connect();
        } catch (Exception $e) {
            $dbConfigExists = false;
        }

        // Map our custom types
        Type::map('params', 'Croogo\Core\Database\Type\ParamsType');
        Type::map('encoded', 'Croogo\Core\Database\Type\EncodedType');
        Type::map('link', 'Croogo\Core\Database\Type\LinkType');

        /**
         * Cache configuration
         */
        $defaultCacheConfig = Cache::getConfig('default');
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
            Cache::setConfig('cached_settings', array_merge(
                Configure::read('Croogo.Cache.defaultConfig'),
                ['groups' => ['settings']]
            ));
        }

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
         * Timezone
         */
        $timezone = Configure::read('Site.timezone');
        if (!$timezone) {
            $timezone = 'UTC';
        }
        date_default_timezone_set($timezone);

        /**
         * Settings
         */
        Configure::config('settings', new DatabaseConfig());
        try {
            Configure::load('settings', 'settings');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error('You can ignore the above error during installation');
        }
    }

    /**
     * @param $app
     * @return void
     */
    public static function croogoBootstrap($app)
    {
        Configure::write(
            'DebugKit.panels',
            array_merge((array)Configure::read('DebugKit.panels'), [
                'Croogo/Core.Plugins',
                'Croogo/Core.ViewHelpers',
                'Croogo/Core.Components',
            ])
        );

        Croogo::hookComponent('*', [
            'Croogo' => [
                'className' => 'Croogo/Core.Croogo',
                'priority' => 5
            ]
        ]);
        Croogo::hookComponent('*', 'Croogo/Acl.Filter');
        Croogo::hookComponent('*', [
            'Security' => [
                'blackHoleCallback' => '_securityError',
            ],
        ]);
        Croogo::hookComponent('*', 'Acl.Acl');
        Croogo::hookComponent('*', 'Auth');
        Croogo::hookComponent('*', 'Flash');
        //Croogo::hookComponent('*', 'RequestHandler');
        Croogo::hookComponent('*', 'Croogo/Core.Theme');

        Croogo::hookHelper('*', 'Croogo/Core.Js');
        Croogo::hookHelper('*', 'Croogo/Core.Layout');

        // Make sure that the Croogo event manager is the global one
        EventManager::instance();

        time(function () {

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
                'Croogo/Settings',
                'Croogo/Acl',
                'Croogo/Blocks',
                'Croogo/Comments',
                'Croogo/Contacts',
                'Croogo/Menus',
                'Croogo/Meta',
                'Croogo/Nodes',
                'Croogo/Taxonomy',
                'Croogo/Users',
                'Croogo/Wysiwyg',
                'Croogo/Ckeditor',
                'Croogo/Dashboards',
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

        time(function () use ($app) {
            /**
             * Load required plugins
             */
            if (!Plugin::isLoaded('Acl')) {
                $app->addPlugin('Acl', ['bootstrap' => true]);
            }
            if (!Plugin::isLoaded('BootstrapUI')) {
                $app->addPlugin('BootstrapUI');
            }

            /**
             * Extensions
             */
            $app->addPlugin('Croogo/Extensions', [
                'autoload' => true,
                'bootstrap' => true,
                'routes' => true,
                'events' => true
            ]);
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
        time(function () use ($app, $plugins, $themes) {
            $option = [
                'autoload' => true,
                'bootstrap' => true,
                'ignoreMissing' => true,
                'routes' => true,
                'events' => true
            ];
            foreach ($plugins as $plugin) {
                $plugin = Inflector::camelize($plugin);
                if (Plugin::isLoaded($plugin)) {
                    continue;
                }

                try {
                    PluginManager::load($plugin, $option);
                } catch (MissingPluginException $e) {
                    Log::error('Plugin not found during bootstrap: ' . $plugin);
                    continue;
                }
            }

            foreach ($themes as $theme) {
                if ($theme && !Plugin::isLoaded($theme) && PluginManager::available($theme)) {
                    PluginManager::load($theme, [
                        'autoload' => true,
                        'bootstrap' => true,
                        'routes' => true,
                        'events' => true,
                        'ignoreMissing' => true
                    ]);
                }
            }
        }, 'plugins-loading-configured', 'Loading configured plugins: ' . implode(', ', $plugins + $themes));

        // FIXME DispatcherFactory::add('Croogo/Core.HomePage');

        time(function () {
            PluginManager::events();

            EventManager::loadListeners();
        }, 'Registering plugin listeners');

        $setupFile = ROOT . '/config/croogo.php';
        if (file_exists ($setupFile)) {
            require_once $setupFile;
        }

        time(function () {
            Croogo::dispatchEvent('Croogo.bootstrapComplete');
        }, 'event-Croogo.bootstrapComplete', 'Event: Croogo.bootstrapComplete');
    }
}
