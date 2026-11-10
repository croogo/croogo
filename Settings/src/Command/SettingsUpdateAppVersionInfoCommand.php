<?php
declare(strict_types=1);

namespace Croogo\Settings\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;

/**
 * Port SettingsShell::updateAppVersionInfo (Cake 5: Shell -> Command).
 *
 * Usage: bin/cake settings update_app_version_info
 */
class SettingsUpdateAppVersionInfoCommand extends Command
{
    public static function defaultName(): string
    {
        return 'settings update_app_version_info';
    }

    public static function getDescription(): string
    {
        return __d('croogo', 'Update app version string from git tag information');
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser->setDescription(static::getDescription());
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        Configure::write('Trackable.Auth.User', ['id' => 1]);
        $result = $this->fetchTable('Croogo/Settings.Settings')->updateAppVersionInfo();

        return $result ? static::CODE_SUCCESS : static::CODE_ERROR;
    }
}
