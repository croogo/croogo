<?php

namespace Croogo\Install;

use Cake\Composer\Installer\PluginInstaller;
use Composer\Composer;
use Composer\Script\Event;

class ComposerInstaller extends PluginInstaller
{

    public static function postAutoloadDump(Event $event)
    {
        $composer = $event->getComposer();
        $config = $composer->getConfig();
        $vendorDir = realpath($config->get('vendor-dir'));
        $croogoDir = dirname(dirname(__DIR__));
        $packages = $composer->getRepositoryManager()->getLocalRepository()->getPackages();
        $pluginsDir = dirname($vendorDir) . DIRECTORY_SEPARATOR . 'plugins';
        $plugins = static::determinePlugins($packages, $pluginsDir, $vendorDir);
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
}
