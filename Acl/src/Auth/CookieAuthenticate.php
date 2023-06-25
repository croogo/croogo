<?php
declare(strict_types=1);

namespace Croogo\Acl\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Core\Exception\Exception;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;

/**
 * An authentication adapter for AuthComponent.  Provides the ability to authenticate using COOKIE
 *
 * {{{
 *  $this->Auth->authenticate = array(
 *      'Authenticate.Cookie' => array(
 *          'fields' => array(
 *              'username' => 'username',
 *              'password' => 'password'
 *          ),
 *          'userModel' => 'User',
 *          'scope' => array('User.active' => 1),
 *          'crypt' => 'rijndael', // Defaults to rijndael(safest), optionally set to 'cipher' if required
 *          'cookie' => array(
 *              'name' => 'RememberMe',
 *              'time' => '+2 weeks',
 *          )
 *      )
 *  )
 * }}}
 *
 * @package     Croogo.Acl.Auth
 * @copyright   Copyright (c) 2012 Ceeram
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @see AclAutoLoginComponent
 */
class CookieAuthenticate extends BaseAuthenticate
{

    /**
     * Cookie configuration
     *
     * FIXME: This config should be centralized/shared with AutoLoginComponent
     */
    protected $_defaultConfig = [
        'cookie' => [
            'name' => 'CAL',
            'time' => '+2 weeks',
        ],
    ];

    /**
     * Verify cookie data
     *
     * return boolean|array User data or boolean False when data is invalid
     */
    protected function _verify(string $cookie)
    {
        $cookie = json_decode($cookie, true);
        if (empty($cookie['data'])) {
            return false;
        }

        $data = $cookie['data'];
        $mac = hash_hmac('sha256', $data, Security::getSalt());
        if ($mac !== $cookie['mac']) {
            return false;
        }

        $data = json_decode($cookie['data'], true);
        $settings = $this->getConfig();
        $fields = $settings['fields'];
        if (empty($data['hash']) ||
            empty($data['time']) ||
            empty($data[$fields['username']])
        ) {
            return false;
        }

        $username = $data[$fields['username']] . $data['time'];
        if ($this->passwordHasher()->check($username, $data['hash'])) {
            return $data;
        }

        return false;
    }

    /**
     * Authenticates the identity contained in the cookie.  Will use the `settings.userModel`, and `settings.fields`
     * to find COOKIE data that is used to find a matching record in the `settings.userModel`.  Will return false if
     * there is no cookie data, either username or password is missing, of if the scope conditions have not been met.
     *
     * @param Request $request The unused request object
     * @return mixed False on login failure. An array of User data on success.
     * @throws \Cake\Core\Exception\CakeException
     */
    public function getUser(ServerRequest $request)
    {
        $config = $this->getConfig();
        if (!function_exists('mcrypt_encrypt') && !function_exists('openssl_encrypt')) {
            throw new \Cake\Core\Exception\CakeException('Cannot use encryption, either mcrypt_encrypt() or openssl_encrypt() is required');
        }

        list(, $model) = pluginSplit($config['userModel']);

        $cookieName = $config['cookie']['name'];
        unset($config['cookie']['name']);
        $cookie = $request->getCookie($cookieName);
        $data = $cookie ? $this->_verify($cookie) : false;
        if (!$data) {
            return false;
        }

        extract($config['fields']);
        if (empty($data[$username])) {
            return false;
        }

        $user = $this->_findUser($data[$username]);
        if ($user) {
            $this->_registry->Auth->setUser($user);

            return $user;
        }

        return false;
    }

    /**
     * Find a user record
     *
     * @see BaseAuthenticate::_findUser()
     */
    protected function _findUser($conditions, $password = null)
    {
        $config = $this->getConfig();
        $userModel = $config['userModel'];
        list(, $model) = pluginSplit($userModel);
        $fields = $config['fields'];

        if (!is_array($conditions)) {
            $username = $conditions;
            $conditions = [
                $model . '.' . $fields['username'] => $username,
            ];
        }
        if (!empty($this->settings['scope'])) {
            $conditions = array_merge($conditions, $this->settings['scope']);
        }

        $query = TableRegistry::getTableLocator()->get($userModel)->find()
            ->where($conditions);

        if (!empty($config['contain'])) {
            $query->contain($config['contain']);
        }

        $user = $query->first()->toArray();
        if (empty($user) || empty($user[$fields['username']])) {
            return false;
        }
        if (isset($conditions[$model . '.' . $fields['password']]) ||
            isset($conditions[$fields['password']])
        ) {
            unset($user[$fields['password']]);
        }

        return $user;
    }

    /**
     * Authenticate a user based on the request information
     *
     * @see BaseAuthenticate::authenticate()
     */
    public function authenticate(ServerRequest $request, Response $response)
    {
        if ($request->getData() || $request->is('post')) {
            return false;
        }

        return $this->getUser($request);
    }
}
