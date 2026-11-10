<?php
declare(strict_types=1);

namespace Croogo\Core\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Utility\Security;

/**
 * Port CroogoShell::password (Cake 5: Shell -> Command).
 *
 * Usage: bin/cake croogo password <password>
 */
class CroogoPasswordCommand extends Command
{
    public static function defaultName(): string
    {
        return 'croogo password';
    }

    public static function getDescription(): string
    {
        return __d('croogo', 'Get hashed password');
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription(static::getDescription())
            ->addArgument('password', [
                'required' => true,
                'help' => __d('croogo', 'Password to hash'),
            ]);
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $value = trim((string)$args->getArgument('password'));
        $io->out(Security::hash($value, null, true));

        return static::CODE_SUCCESS;
    }
}
