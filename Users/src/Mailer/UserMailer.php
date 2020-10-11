<?php
declare(strict_types=1);
declare(strict_types=1);

namespace Croogo\Users\Mailer;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
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
        $this->viewBuilder()->setTemplate('Croogo/Users.forgot_password');
        return $this
            ->setProfile('default')
            ->setTo($user->email)
            ->setSubject(__d('croogo', '[%s] Reset Password', Configure::read('Site.title')))
            ->setEmailFormat('both')
            ->setViewVars([
                'user' => $user
            ]);
    }

    public function registrationActivation(User $user)
    {
        $this->viewBuilder()->setTemplate('Croogo/Users.register');
        return $this
            ->setProfile('default')
            ->setTo($user->email)
            ->setSubject(__d('croogo', '[%s] Please activate your account', Configure::read('Site.title')))
            ->setEmailFormat('both')
            ->setViewVars([
                'user' => $user
            ]);
    }

    public function onRegistration(EventInterface $event, User $user)
    {
        $this->send('registrationActivation', [$user]);
    }
}
