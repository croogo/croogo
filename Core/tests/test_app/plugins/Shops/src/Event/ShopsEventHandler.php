<?php

namespace Shops\Event;

use Cake\Event\EventListenerInterface;

class ShopsEventHandler implements EventListenerInterface
{

    public function implementedEvents()
    {
        return [
            'Controller.Users.activationFailure' => [
                'callable' => 'onActivationFailure',
            ],
            'Controller.Users.activationSuccessful' => [
                'callable' => 'onActivationSuccessful',
            ],
            'Controller.Users.adminLoginSuccessful' => [
                'callable' => 'onAdminLoginSuccessful',
            ],
            'Controller.Users.adminLoginFailure' => [
                'callable' => 'onAdminLoginFailure',
            ],
            'Controller.Users.adminLogoutSuccessful' => [
                'callable' => 'onAdminLogoutSuccessful',
            ],
            'Controller.Users.afterLogout' => [
                'callable' => 'onAfterLogout',
            ],
            'Controller.Users.beforeLogin' => [
                'callable' => 'onBeforeLogin',
            ],
            'Controller.Users.beforeLogout' => [
                'callable' => 'onBeforeLogout',
            ],
            'Controller.Users.loginFailure' => [
                'callable' => 'onLoginFailure',
            ],
            'Controller.Users.loginSuccessful' => [
                'callable' => 'onLoginSuccessful',
            ],
            'Controller.Users.registrationFailure' => [
                'callable' => 'onRegistrationFailure',
            ],
            'Controller.Users.registrationSuccessful' => [
                'callable' => 'onRegistrationSuccessful',
            ],

            'Helper.Layout.beforeFilter' => [
                'callable' => 'onLayoutBeforeFilter',
            ],
            'Helper.Layout.afterFilter' => [
                'callable' => 'onLayoutAfterFilter',
            ],
        ];
    }

    public function onActivationFailure($event)
    {
        return true;
    }

    public function onActivationSuccessful($event)
    {
        return true;
    }

    public function onAfterLogout($event)
    {
        return true;
    }

    public function onBeforeLogin($event)
    {
        return true;
    }

    public function onBeforeLogout($event)
    {
        return true;
    }

    public function onLoginFailure($event)
    {
        return true;
    }

    public function onLoginSuccessful($event)
    {
        return true;
    }

    public function onAdminLoginSuccessful($event)
    {
        return true;
    }

    public function onAdminLoginFailure($event)
    {
        return true;
    }

    public function onAdminLogoutSuccessful($event)
    {
        return true;
    }

    public function onRegistrationFailure($event)
    {
        return true;
    }

    public function onRegistrationSuccessful($event)
    {
        return true;
    }

    public function onLayoutBeforeFilter($event)
    {
        return true;
    }

    public function onLayoutAfterFilter($event)
    {
        return true;
    }
}
