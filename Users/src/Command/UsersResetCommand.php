<?php
declare(strict_types=1);

namespace Croogo\Users\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;

/**
 * Port UsersShell::reset (Cake 5: Shell -> Command).
 *
 * Usage: bin/cake users reset <username> <password>
 */
class UsersResetCommand extends Command
{
    public static function defaultName(): string
    {
        return 'users reset';
    }

    public static function getDescription(): string
    {
        return __d('croogo', 'Reset user password');
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription(static::getDescription())
            ->addArgument('username', [
                'required' => true,
                'help' => __d('croogo', 'Username to reset'),
            ])
            ->addArgument('password', [
                'required' => true,
                'help' => __d('croogo', 'New user password'),
            ]);
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        Configure::write('Trackable.Auth.User', ['id' => 1]);
        $Users = $this->fetchTable('Croogo/Users.Users');

        $username = (string)$args->getArgument('username');
        $password = (string)$args->getArgument('password');

        $user = $Users->findByUsername($username)->first();
        if (empty($user)) {
            $io->warning(__d('croogo', 'User \'%s\' not found', $username));

            return static::CODE_ERROR;
        }
        $user->clean();
        $user->password = $password;
        $result = $Users->save($user);
        if ($result) {
            $io->success(__d('croogo', 'Password for \'%s\' has been changed', $username));

            return static::CODE_SUCCESS;
        }

        return static::CODE_ERROR;
    }
}
