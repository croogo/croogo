<?php

namespace Croogo\Users\Controller\Api\V10;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;
use Cake\Utility\Security;
use Croogo\Core\Controller\Api\AppController;
use Firebase\JWT\JWT;

/**
 * Users Controller
 */
class UsersController extends AppController
{

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow('token');
    }

    public function lookup()
    {
        return $this->Crud->execute();
    }

    protected function generateToken($user)
    {
        $payload = [
            'username' => $user['username'],
            'name' => $user['name'],
            'timezone' => $user['timezone'],
        ];

        $expiry = 10 * 24 * 3600; // 10days
        $buffer = 5 * 60; // 5mins for random
        $exp = time() + rand($expiry, $expiry + $buffer);
        return JWT::encode([
            'iss' => Configure::read('Site.title'),
            'sub' => $user['id'],
            'user' => $payload,
            'iat' => time(),
            'exp' => $exp,
        ], Security::salt());
    }

    public function token()
    {
        $user = $this->Auth->identify();
        if ($user) {
            $token = $this->generateToken($user);
        } else {
            throw new NotFoundException();
        }

        $this->set([
            'data' => [
                'token' => $token,
            ],
            '_serialize' => ['data'],
        ]);
    }

}
