<?php
declare(strict_types=1);

namespace Croogo\Core\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Croogo\Acl\AclGenerator;

/**
 * Port CroogoShell::syncContentAcos (Cake 5: Shell -> Command).
 *
 * Usage: bin/cake croogo sync_content_acos
 */
class CroogoSyncContentAcosCommand extends Command
{
    public static function defaultName(): string
    {
        return 'croogo sync_content_acos';
    }

    public static function getDescription(): string
    {
        return __d('croogo', 'Populate acos of existing contents');
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser->setDescription(static::getDescription());
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $aclGenerator = new AclGenerator();
        $aclGenerator->setShell($io);
        $aclGenerator->syncContentAcos();

        return static::CODE_SUCCESS;
    }
}
