<?php
declare(strict_types=1);

namespace Croogo\Acl\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Exception\Exception;
use Cake\Http\Response;
use Cake\Http\ResponseEmitter;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;

/**
 * An authentication adapter for AuthComponent.  Provides the ability to authenticate using Token
 *
 * {{{
 *  $this->Auth->authenticate = array(
 *      'Authenticate.Token' => array(
 *          'fields' => array(
 *              'username' => 'username',
 *              'password' => 'password',
 *              'token' => 'public_key',
 *          ),
 *          'parameter' => '_token',
 *          'header' => 'X-MyApiTokenHeader',
 *          'userModel' => 'User',
 *          'scope' => array('User.active' => 1)
 *      )
 *  )
 * }}}
 *
 * @package     Croogo.Acl.Controller.Component.Auth
 */
class TokenAuthenticate extends BaseAuthenticate
{
    /**
     * Settings for this object.
     *
     * - `fields` The fields to use to identify a user by. Make sure `'token'` has been added to the array
     * - `parameter` The url parameter name of the token.
     * - `header` The token header value.
     * - `userModel` The model name of the User, defaults to User.
     * - `scope` Additional conditions to use when looking up and authenticating users,
     *    i.e. `array('User.is_active' => 1).`
     * - `recursive` The value of the recursive key passed to find(). Defaults to 0.
     * - `contain` Extra models to contain and store in session.
     *
     * @var array
     */
    public $settings = [
        'fields' => [
            'username' => 'username',
            'password' => 'password',
            'token' => 'token',
        ],
        'parameter' => '_token',
        'header' => 'X-ApiToken',
        'userModel' => 'Croogo/Users.Users',
        'scope' => [],
        'recursive' => 0,
        'contain' => null,
    ];

    /**
     * Constructor
     *
     * @param ComponentRegistry $collection The Component collection used on this request.
     * @param array $settings Array of settings to use.
     * @throws Exception
     */
    public function __construct(ComponentRegistry $collection, $settings)
    {
        parent::__construct($collection, $settings);
        if (empty($this->settings['parameter']) && empty($this->settings['header'])) {
            throw new Exception(__d('croogo', 'You need to specify token parameter and/or header'));
        }
    }

    /**
     *
     * @param Request $request The request object
     * @param Response $response response object.
     * @return mixed.  False on login failure.  An array of User data on success.
     */
    public function authenticate(ServerRequest $request, Response $response)
    {
        $user = $this->getUser($request);
        if (!$user) {
            $response = $response->withStatus(401);
            $emitter = new ResponseEmitter();
            $emitter->emit($response);
        }

        return $user;
    }

    /**
     * Get token information from the request.
     *
     * @param \Cake\Http\ServerRequest $request Request object.
     * @return mixed Either false or an array of user information
     */
    public function getUser(ServerRequest $request)
    {
        if (!empty($this->settings['header'])) {
            $token = current($request->getHeader($this->settings['header']));
            if ($token) {
                return $this->_findUser($token, null);
            }
        }
        if (!empty($this->settings['parameter']) && !empty($request->query[$this->settings['parameter']])) {
            $token = $request->query[$this->settings['parameter']];

            return $this->_findUser($token);
        }

        return false;
    }

    /**
     * Find a user record.
     *
     * @param string $username The token identifier.
     * @param string $password Unused password.
     * @return array|bool Either false on failure, or an array of user data.
     */
    protected function _findUser($username, $password = null)
    {
        $userModel = $this->settings['userModel'];
        list($plugin, $model) = pluginSplit($userModel);
        $fields = $this->settings['fields'];

        $conditions = [
            $model . '.' . $fields['token'] => $username,
        ];
        if (!empty($this->settings['scope'])) {
            $conditions = array_merge($conditions, $this->settings['scope']);
        }
        $user = TableRegistry::getTableLocator()->get($userModel)->find()
            ->where($conditions)
            ->contain($this->settings['contain'])
            ->first();
        if (!$user) {
            return false;
        }
        $user->unset('password');

        return $user->toArray();
    }

    public function unauthenticated(ServerRequest $request, Response $response)
    {
        return $response->withStatus(401);
    }
}
