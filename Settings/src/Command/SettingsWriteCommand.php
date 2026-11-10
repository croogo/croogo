<?php
declare(strict_types=1);

namespace Croogo\Settings\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;

/**
 * Port SettingsShell::write (Cake 5: Shell -> Command).
 *
 * Usage: bin/cake settings write <key> <value> [-c] [-t title] [-d desc] [-i input_type] [-e editable] [-p params]
 */
class SettingsWriteCommand extends Command
{
    public static function defaultName(): string
    {
        return 'settings write';
    }

    public static function getDescription(): string
    {
        return __d('croogo', 'Write setting value for a given key');
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription(static::getDescription())
            ->addArgument('key', [
                'help' => __d('croogo', 'Setting key'),
                'required' => true,
            ])
            ->addArgument('value', [
                'help' => __d('croogo', 'Setting value'),
                'required' => true,
            ])
            ->addOption('create', [
                'boolean' => true,
                'short' => 'c',
            ])
            ->addOption('title', [
                'short' => 't',
            ])
            ->addOption('description', [
                'short' => 'd',
            ])
            ->addOption('input_type', [
                'choices' => [
                    'text', 'textarea', 'checkbox', 'multiple',
                    'radio', 'file',
                ],
                'short' => 'i',
            ])
            ->addOption('editable', [
                'short' => 'e',
                'choices' => ['1', '0', 'y', 'n', 'Y', 'N'],
            ])
            ->addOption('params', [
                'short' => 'p',
            ]);
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        Configure::write('Trackable.Auth.User', ['id' => 1]);
        $key = (string)$args->getArgument('key');
        $val = (string)$args->getArgument('value');

        $Settings = $this->fetchTable('Croogo/Settings.Settings');
        $setting = $Settings->find()
            ->select(['id', 'key', 'value'])
            ->where(['Settings.key' => $key])
            ->first();
        $io->out(__d('croogo', 'Updating %s', $key), 2);
        $ask = __d('croogo', 'Confirm update');
        if ($setting || $args->getOption('create')) {
            $text = '-';
            if ($setting) {
                $text = __d('croogo', '- %s', $setting->value);
            }
            $io->warning($text);
            $io->success(__d('croogo', '+ %s', $val));

            if ('y' == $io->askChoice($ask, ['y', 'n'], 'n')) {
                $options = [];
                foreach (['title', 'description', 'input_type', 'editable', 'params'] as $name) {
                    if ($args->getOption($name) !== null) {
                        $options[$name] = $args->getOption($name);
                    }
                }

                if (isset($options['editable'])) {
                    $options['editable'] = in_array(
                        $options['editable'],
                        ['y', 'Y', '1']
                    );
                }

                $Settings->write($key, $val, $options);
                $io->success(__d('croogo', 'Setting updated'));
            } else {
                $io->warning(__d('croogo', 'Cancelled'));
            }
        } else {
            $io->warning(__d('croogo', 'Key: %s not found', $key));
        }

        return static::CODE_SUCCESS;
    }
}
