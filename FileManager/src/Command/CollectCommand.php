<?php
declare(strict_types=1);

namespace Croogo\FileManager\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Port CollectShell (Cake 5: Shell -> Command).
 *
 * Usage: bin/cake collect <dir> [-r regex]
 */
class CollectCommand extends Command
{
    public static function defaultName(): string
    {
        return 'collect';
    }

    public static function getDescription(): string
    {
        return __d('croogo', 'Scan directory and import record to database');
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription(static::getDescription())
            ->addArgument('dir', [
                'help' => __d('croogo', 'Path to scan'),
                'required' => true,
            ])
            ->addOption('regex', [
                'help' => __d('croogo', 'File name Regex'),
                'short' => 'r',
            ]);
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $dir = (string)$args->getArgument('dir');
        $regex = '.*\.(jpg)|(jpeg)|(png)|(pdf)|(mp4)';
        if (strpos($dir, ',') !== false) {
            $dir = explode(',', $dir);
        }
        if ($args->getOption('regex') !== null) {
            $regex = (string)$args->getOption('regex');
        }
        $Attachment = $this->fetchTable('Croogo/FileManager.Attachments');
        $importTask = $Attachment->importTask((array)$dir, $regex);
        if (!empty($importTask['error'])) {
            $io->out('<error>Warnings/Errors:</error>');
            $tasks = $errors = 0;
            foreach ($importTask['error'] as $message) {
                $tasks++;
                if ($message) {
                    $io->err("\t$message");
                    $errors++;
                }
            }
            $io->out();
            if ($tasks - $errors > 0) {
                $io->out('<warning>' . __d('croogo', 'Task has %s tasks and %s errors?', $tasks, $errors) . '</warning>');
                $continue = $io->askChoice('Continue?', ['Y', 'n'], 'n');
                if ($continue == 'n') {
                    $io->out('Aborted');

                    return static::CODE_ERROR;
                }
            }
        }
        $result = $Attachment->runTask($importTask);

        $message = __d('croogo', 'Processed %s files with %s errors', $result['imports'], $result['errors']);
        if ($result['errors'] == 0) {
            $message = sprintf('<warning>%s</warning>', $message);
            $io->out($message);
        } else {
            $message = sprintf('<error>%s</error>', $message);
            $io->err($message);
        }

        return static::CODE_SUCCESS;
    }
}
