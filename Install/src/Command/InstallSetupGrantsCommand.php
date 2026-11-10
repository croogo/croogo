<?php
declare(strict_types=1);

namespace Croogo\Install\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Plugin;
use Croogo\Core\PluginManager;
use Croogo\Install\InstallManager;

/**
 * Port subkomendy install setup_grants (Cake 5: Shell -> Command).
 *
 * Usage: bin/cake install setup_grants
 */
class InstallSetupGrantsCommand extends Command
{
    public static function defaultName(): string
    {
        return 'install setup_grants';
    }

    public static function getDescription(): string
    {
        return __d('croogo', 'Setup default grants (ACOs) for roles');
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser->setDescription(static::getDescription());
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $options = ['bootstrap' => true, 'routes' => true];
        $plugins = array_merge(PluginManager::$corePlugins, PluginManager::$bundledPlugins);
        foreach ($plugins as $plugin) {
            if (!Plugin::isLoaded($plugin)) {
                PluginManager::load($plugin, $options);
            }
        }

        $InstallManager = new InstallManager();
        $InstallManager->setupGrants(
            function ($message) use ($io): void {
                $io->success((string)$message);
            },
            function ($message) use ($io): void {
                $io->err((string)$message);
            }
        );

        return static::CODE_SUCCESS;
    }
}
