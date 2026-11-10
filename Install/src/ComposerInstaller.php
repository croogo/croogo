<?php

namespace Croogo\Install;

use Composer\Composer;
use Composer\Script\Event;
use DirectoryIterator;

/**
 * Class ComposerInstaller
 *
 * Cake 5 / plugin-installer 2.x: PluginInstaller usunięty, a postAutoloadDump
 * i tak niczego nie dziedziczył — samodzielna klasa statyczna.
 */
class ComposerInstaller
{

    /**
     * @param Event $event
     * @return void
     */
    public static function postAutoloadDump(Event $event): void
    {
        $composer = $event->getComposer();
        $config = $composer->getConfig();
        $vendorDir = realpath($config->get('vendor-dir'));

        $corePlugins = [
            'Acl', 'Core', 'Dashboards', 'Extensions', 'FileManager', 'Install', 'Menus',
            'Settings', 'Users'
        ];

        $plugins = [];
        // Shim ACL (plugin `Acl`) — zvendorowany cakephp/acl w Acl/acl-compat.
        // Uwaga: używamy dosłownego '/' zamiast DIRECTORY_SEPARATOR. Na Windows separator
        // to '\', więc wpis kończył się na "...\'," — w apostrofach PHP \' to escape'owany
        // apostrof, string się nie zamykał i cakephp-plugins.php miał parse error.
        $plugins[] = "\t\t'Acl' => \$baseDir . '/vendor/croogo/croogo/Acl/acl-compat/',";
        foreach ($corePlugins as $plugin) {
            $plugins[] = "\t\t'Croogo/{$plugin}' => \$baseDir . '/vendor/croogo/croogo/{$plugin}/',";
        }
        $plugins[] = "\t],";

        $cakephp_plugins = file_get_contents( $vendorDir . DIRECTORY_SEPARATOR . 'cakephp-plugins.php' );

        if ($cakephp_plugins !== false) {
            $cakephp_plugins = str_replace("],", implode("\n", $plugins), $cakephp_plugins);
            file_put_contents($vendorDir . DIRECTORY_SEPARATOR . 'cakephp-plugins.php', $cakephp_plugins);
        }
    }
}
