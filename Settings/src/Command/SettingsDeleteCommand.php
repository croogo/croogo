<?php
declare(strict_types=1);

namespace Croogo\Settings\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;

/**
 * Port SettingsShell::delete (Cake 5: Shell -> Command).
 *
 * Usage: bin/cake settings delete <key>
 */
class SettingsDeleteCommand extends Command
{
    public static function defaultName(): string
    {
        return 'settings delete';
    }

    public static function getDescription(): string
    {
        return __d('croogo', 'Delete setting based on key');
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription(static::getDescription())
            ->addArgument('key', [
                'help' => __d('croogo', 'Setting key'),
                'required' => true,
            ]);
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        Configure::write('Trackable.Auth.User', ['id' => 1]);
        $key = (string)$args->getArgument('key');

        $Settings = $this->fetchTable('Croogo/Settings.Settings');
        $setting = $Settings->find()
            ->select(['id', 'key', 'value'])
            ->where(['Settings.key' => $key])
            ->first();
        $io->out(__d('croogo', 'Deleting %s', $key), 2);
        $ask = __d('croogo', 'Delete?');
        if ($setting) {
            if ('y' == $io->askChoice($ask, ['y', 'n'], 'n')) {
                $Settings->deleteKey($setting->key);
                $io->success(__d('croogo', 'Setting deleted'));
            } else {
                $io->warning(__d('croogo', 'Cancelled'));
            }
        } else {
            $io->warning(__d('croogo', 'Key: %s not found', $key));
        }

        return static::CODE_SUCCESS;
    }
}
