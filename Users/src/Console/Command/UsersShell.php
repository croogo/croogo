<?php

/**
 * UsersShell
 *
 * @package Croogo.Users.Console.Command
 */
namespace Croogo\Users\Console\Command;

class UsersShell extends AppShell
{

    public $uses = [
        'Users.User',
    ];

/**
 * getOptionParser
 */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->addSubCommand('reset', [
                'help' => __d('croogo', 'Reset user password'),
                'parser' => [
                    'arguments' => [
                        'username' => [
                            'required' => true,
                            'help' => __d('croogo', 'Username to reset'),
                        ],
                        'password' => [
                            'required' => true,
                            'help' => __d('croogo', 'New user password'),
                        ],
                    ],
                ],
            ]);
    }

/**
 * reset
 */
    public function reset()
    {
        $username = $this->args[0];
        $password = $this->args[1];

        $user = $this->User->findByUsername($username);
        if (empty($user['User']['id'])) {
            return $this->warn(__d('croogo', 'User \'%s\' not found', $username));
        }
        $this->User->id = $user['User']['id'];
        $result = $this->User->saveField('password', $password);
        if ($result) {
            $this->success(__d('croogo', 'Password for \'%s\' has been changed', $username));
        }
    }
}
