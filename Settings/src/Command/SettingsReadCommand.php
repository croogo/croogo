<?php
declare(strict_types=1);

namespace Croogo\Settings\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;

/**
 * Port SettingsShell::read (Cake 5: Shell -> Command).
 *
 * Usage: bin/cake settings read [key] [-a]
 */
class SettingsReadCommand extends Command
{
    public static function defaultName(): string
    {
        return 'settings read';
    }

    public static function getDescription(): string
    {
        return __d('croogo', 'Displays setting values');
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription(static::getDescription())
            ->addArgument('key', [
                'help' => __d('croogo', 'Setting key'),
                'required' => false,
            ])
            ->addOption('all', [
                'help' => __d('croogo', 'List all settings'),
                'short' => 'a',
                'boolean' => true,
            ]);
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        Configure::write('Trackable.Auth.User', ['id' => 1]);
        $key = $args->getArgument('key');
        if ($key === null && !$args->getOption('all')) {
            $io->err(__d('croogo', 'Provide a key or use --all'));

            return static::CODE_ERROR;
        }

        $settings = $this->fetchTable('Croogo/Settings.Settings')->find()
            ->where(['Settings.key LIKE' => '%' . (string)$key . '%'])
            ->orderByAsc('Settings.weight');
        $io->out('Settings: ', 2);
        foreach ($settings as $data) {
            $io->out(__d('croogo', "    %-30s: %s", $data->key, $data->value));
        }
        $io->out();

        return static::CODE_SUCCESS;
    }
}
