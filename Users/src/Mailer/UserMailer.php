<?php
declare(strict_types=1);
declare(strict_types=1);

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
        return $this
            ->setProfile('default')
            ->setTo($user->email)
            ->setSubject(__d('croogo', '[%s] Reset Password', Configure::read('Site.title')))
            ->setEmailFormat('both')
            ->setViewVars([
                'user' => $user
            ])
            ->viewBuilder()->setTemplate('Croogo/Users.forgot_password');
    }

    public function registrationActivation(User $user)
    {
        return $this
            ->setProfile('default')
            ->setTo($user->email)
            ->setSubject(__d('croogo', '[%s] Please activate your account', Configure::read('Site.title')))
            ->setEmailFormat('both')
            ->setViewVars([
                'user' => $user
            ])
            ->viewBuilder()->setTemplate('Croogo/Users.register');
    }

    public function onRegistration(Event $event, User $user)
    {
        $this->send('registrationActivation', [$user]);
    }
}
