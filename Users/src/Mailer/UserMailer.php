<?php

namespace Croogo\Users\Mailer;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Mailer\Mailer;
use Croogo\Users\Model\Entity\User;

class UserMailer extends Mailer
{

    public $layout = 'default';

    public function implementedEvents(): array
    {
        return [
            'Users.registered' => 'onRegistration'
        ];
    }

    public function resetPassword(User $user)
    {
        $this
            ->setProfile('default')
            ->setTo($user->email)
            ->setSubject(__d('croogo', '[%s] Reset Password', Configure::read('Site.title')))
            ->setEmailFormat('both')
            ->setViewVars([
                'user' => $user,
            ]);
        $this->viewBuilder()->setTemplate('Croogo/Users.forgot_password');

        return $this;
    }

    public function registrationActivation(User $user)
    {
        $this
            ->setProfile('default')
            ->setTo($user->email)
            ->setSubject(__d('croogo', '[%s] Please activate your account', Configure::read('Site.title')))
            ->setEmailFormat('both')
            ->setViewVars([
                'user' => $user,
            ]);
        $this->viewBuilder()->setTemplate('Croogo/Users.register');

        return $this;
    }

    public function onRegistration(Event $event, User $user): void
    {
        $this->send('registrationActivation', [$user]);
    }
}
