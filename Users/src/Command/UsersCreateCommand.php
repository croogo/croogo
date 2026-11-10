<?php
declare(strict_types=1);

namespace Croogo\Users\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Croogo\Users\Model\Entity\User;

/**
 * Port UsersShell::create (Cake 5: Shell -> Command).
 *
 * Usage: bin/cake users create <username> <password> <role_id>
 */
class UsersCreateCommand extends Command
{
    public static function defaultName(): string
    {
        return 'users create';
    }

    public static function getDescription(): string
    {
        return __d('croogo', 'Create a new user');
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription(static::getDescription())
            ->addArgument('username', [
                'required' => true,
                'help' => __d('croogo', 'Username'),
            ])
            ->addArgument('password', [
                'required' => true,
                'help' => __d('croogo', 'New user password'),
            ])
            ->addArgument('role_id', [
                'required' => true,
                'help' => __d('croogo', 'Role id for user'),
            ]);
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        Configure::write('Trackable.Auth.User', ['id' => 1]);
        $Users = $this->fetchTable('Croogo/Users.Users');

        $username = (string)$args->getArgument('username');
        $password = (string)$args->getArgument('password');
        $roleId = $args->getArgument('role_id');

        $user = $Users->findByUsername($username)->first();
        if ($user) {
            $io->warning(__d('croogo', 'User \'%s\' already exists', $username));

            return static::CODE_ERROR;
        }

        $user = new User([
            'username' => $username,
            'password' => $password,
            'role_id' => $roleId,
            'name' => $username,
            'email' => $username,
            'activation_key' => $Users->generateActivationKey(),
            'status' => true,
        ]);
        $result = $Users->save($user);
        if ($result) {
            $io->success(__d('croogo', 'User \'%s\' has been created', $username));

            return static::CODE_SUCCESS;
        }

        return static::CODE_ERROR;
    }
}
