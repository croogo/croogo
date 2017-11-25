<?php

namespace Croogo\Users\Shell;

use Cake\Console\Shell;
use Croogo\Users\Model\Entity\User;

/**
 * UsersShell
 *
 * @package Croogo.Users.Shell
 */
class UsersShell extends Shell
{

    public $uses = [
        'Users.User',
    ];

    /**
     * Initialize
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Croogo/Users.Users');
    }

    /**
     * getOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->addSubCommand('create', [
                'help' => __d('croogo', 'Create a new user'),
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
                        'role_id' => [
                            'required' => true,
                            'help' => __d('croogo', 'Role id for user'),
                        ],
                    ],
                ],
            ])
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

        $user = $this->Users->findByUsername($username)->first();
        if (empty($user)) {
            return $this->warn(__d('croogo', 'User \'%s\' not found', $username));
        }
        $user->clean();
        $user->password = $password;
        $result = $this->Users->save($user);
        if ($result) {
            $this->success(__d('croogo', 'Password for \'%s\' has been changed', $username));
        }
    }

    /**
     * reset
     */
    public function create()
    {
        $username = $this->args[0];
        $password = $this->args[1];
        $roleId = $this->args[2];

        $user = $this->Users->findByUsername($username)->first();
        if ($user) {
            return $this->warn(__d('croogo', 'User \'%s\' already exists', $username));
        }

        $user = new User([
            'username' => $username,
            'password' => $password,
            'role_id' => $roleId,
            'name' => $username,
            'email' => $username,
            'activation_key' => $this->Users->generateActivationKey(),
            'status' => true,
        ]);
        $result = $this->Users->save($user);
        if ($result) {
            $this->success(__d('croogo', 'User \'%s\' has been created', $username));
        }
    }

}
