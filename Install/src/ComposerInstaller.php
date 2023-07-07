<?php
declare(strict_types=1);

namespace Croogo\Install;

use Cake\Composer\Installer\PluginInstaller;
use Composer\Composer;
use Composer\Script\Event;
use DirectoryIterator;

/**
 * Class ComposerInstaller
 */
class ComposerInstaller extends PluginInstaller
{

    /**
     * Called whenever composer (re)generates the autoloader.
     *
     * Recreates CakePHP's plugin path map, based on composer information
     * and available app plugins.
     *
     * @param \Composer\Script\Event $event Composer's event object.
     * @return void
     */
    public function postAutoloadDump(Event $event)
    {
        $composer = $event->getComposer();
        $config = $composer->getConfig();

        $vendorDir = realpath($config->get('vendor-dir'));

        $croogoDir = dirname(dirname(__DIR__));

        $packages = $composer->getRepositoryManager()->getLocalRepository()->getPackages();
        $extra = $event->getComposer()->getPackage()->getExtra();
        if (empty($extra['plugin-paths'])) {
            $pluginDirs = [dirname($vendorDir) . DIRECTORY_SEPARATOR . 'plugins'];
        } else {
            $pluginDirs = $extra['plugin-paths'];
        }

        $plugins = $this->findPlugins($packages, $pluginsDir, $vendorDir);

        $corePlugins = [
            'Acl', 'Blocks', 'Comments', 'Contacts', 'Core', 'Dashboards',
            'Example', 'Extensions', 'FileManager', 'Install', 'Menus',
            'Meta', 'Nodes', 'Settings', 'Taxonomy', 'Translate', 'Users',
            'Wysiwyg',
        ];
        foreach ($corePlugins as $plugin) {
            $plugins['Croogo\\' . $plugin] = $croogoDir . DIRECTORY_SEPARATOR . $plugin;
        }

        $configFile = static::_configFile($vendorDir);
        static::writeConfigFile($configFile, $plugins);
    }

    /**
     * Find all available plugins.
     *
     * Add all composer packages of type `cakephp-plugin`, and all plugins located
     * in the plugins directory to a plugin-name indexed array of paths.
     *
     * @param \Composer\Package\PackageInterface[] $packages Array of \Composer\Package\PackageInterface objects.
     * @param array $pluginDirs The path to the plugins dir.
     * @param string $vendorDir The path to the vendor dir.
     * @return array Plugin name indexed paths to plugins.
     */
    public function findPlugins(
        array $packages,
        array $pluginDirs = ['plugins'],
        $vendorDir = 'vendor'
    ) {
        $plugins = [];

        foreach ($packages as $package) {
            if (!in_array($package->getType(), ['cakephp-plugin', 'croogo-plugin', 'croogo-theme'])) {
                continue;
            }

            $ns = $this->getPrimaryNamespace($package);
            $path = $vendorDir . DIRECTORY_SEPARATOR . $package->getPrettyName();
            $plugins[$ns] = $path;
        }

        foreach ($pluginDirs as $path) {
            $path = $this->getFullPath($path, $vendorDir);
            if (is_dir($path)) {
                $dir = new \DirectoryIterator($path);
                foreach ($dir as $info) {
                    if (!$info->isDir() || $info->isDot()) {
                        continue;
                    }

                    $name = $info->getFilename();
                    if ($name[0] === '.') {
                        continue;
                    }

                    $plugins[$name] = $path . DIRECTORY_SEPARATOR . $name;
                }
            }
        }

        ksort($plugins);

        return $plugins;
    }
}
