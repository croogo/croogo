<?php

namespace Croogo\Users\Shell;

use Cake\Console\Shell;

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

}
