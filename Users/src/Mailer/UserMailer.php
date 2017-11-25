<?php

namespace Croogo\Users\Mailer;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Mailer\Mailer;
use Croogo\Users\Model\Entity\User;

class UserMailer extends Mailer
{

    public $layout = 'default';

    public function implementedEvents()
    {
        return [
            'Users.registered' => 'onRegistration'
        ];
    }

    public function resetPassword(User $user)
    {
        return $this
            ->profile('default')
            ->to($user->email)
            ->subject(__d('croogo', '[%s] Reset Password', Configure::read('Site.title')))
            ->template('Croogo/Users.forgot_password')
            ->emailFormat('both')
            ->set([
                'user' => $user
            ]);
    }

    public function registrationActivation(User $user)
    {
        return $this
            ->profile('default')
            ->to($user->email)
            ->subject(__d('croogo', '[%s] Please activate your account', Configure::read('Site.title')))
            ->template('Croogo/Users.register')
            ->emailFormat('both')
            ->set([
            'user' => $user
            ]);
    }

    public function onRegistration(Event $event, User $user)
    {
        $this->send('registrationActivation', [$user]);
    }
}
