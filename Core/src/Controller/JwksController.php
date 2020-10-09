<?php
declare(strict_types=1);

namespace Croogo\Core\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Firebase\JWT\JWT;

/**
 * Jwks Controller
 *
 */
class JwksController extends AppController
{

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['index']);
    }

    /**
     * Jwks
     */
    public function index()
    {
        if (!file_exists(CONFIG . '/jwt.pem')) {
            throw new NotFoundException();
        }
        $pubKey = file_get_contents(CONFIG . '/jwt.pem');
        $res = openssl_pkey_get_public($pubKey);
        $detail = openssl_pkey_get_details($res);
        $key = [
            'kid' => sha1($pubKey),
            'kty' => 'RSA',
            'alg' => 'RS256',
            'use' => 'sig',
            'e' => JWT::urlsafeB64Encode($detail['rsa']['e']),
            'n' => JWT::urlsafeB64Encode($detail['rsa']['n']),
        ];
        $keys['keys'][] = $key;

        $this->viewBuilder()->setClassName('Json');
        $this->set(compact('keys'));
        $this->viewBuilder()->setOption('serialize', 'keys');
    }

}
