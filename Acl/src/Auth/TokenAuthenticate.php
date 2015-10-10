<?php
namespace Croogo\Acl\Controller\Component\Auth;

use App\Controller\Component\Auth\BaseAuthenticate;
/**
 * An authentication adapter for AuthComponent.  Provides the ability to authenticate using Token
 *
 * {{{
 *	$this->Auth->authenticate = array(
 *		'Authenticate.Token' => array(
 *			'fields' => array(
 *				'username' => 'username',
 *				'password' => 'password',
 *				'token' => 'public_key',
 *			),
 *			'parameter' => '_token',
 *			'header' => 'X-MyApiTokenHeader',
 *			'userModel' => 'User',
 *			'scope' => array('User.active' => 1)
 *		)
 *	)
 * }}}
 *
 * @package     Croogo.Acl.Controller.Component.Auth
 */
class TokenAuthenticate extends BaseAuthenticate {

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
	public $settings = array(
		'fields' => array(
			'username' => 'username',
			'password' => 'password',
			'token' => 'token',
		),
		'parameter' => '_token',
		'header' => 'X-ApiToken',
		'userModel' => 'User',
		'scope' => array(),
		'recursive' => 0,
		'contain' => null,
	);

/**
 * Constructor
 *
 * @param ComponentRegistry $collection The Component collection used on this request.
 * @param array $settings Array of settings to use.
 * @throws CakeException
 */
	public function __construct(ComponentRegistry $collection, $settings) {
		parent::__construct($collection, $settings);
		if (empty($this->settings['parameter']) && empty($this->settings['header'])) {
			throw new CakeException(__d('authenticate', 'You need to specify token parameter and/or header'));
		}
	}

/**
 *
 * @param Request $request The request object
 * @param Response $response response object.
 * @return mixed.  False on login failure.  An array of User data on success.
 */
	public function authenticate(Request $request, Response $response) {
		$user = $this->getUser($request);
		if (!$user) {
			$response->statusCode(401);
			$response->send();
		}
		return $user;
	}

/**
 * Get token information from the request.
 *
 * @param Request $request Request object.
 * @return mixed Either false or an array of user information
 */
	public function getUser(Request $request) {
		if (!empty($this->settings['header'])) {
			$token = $request->header($this->settings['header']);
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
 * @return Mixed Either false on failure, or an array of user data.
 */
	protected function _findUser($username, $password = null) {
		$userModel = $this->settings['userModel'];
		list($plugin, $model) = pluginSplit($userModel);
		$fields = $this->settings['fields'];

		$conditions = array(
			$model . '.' . $fields['token'] => $username,
		);
		if (!empty($this->settings['scope'])) {
			$conditions = array_merge($conditions, $this->settings['scope']);
		}
		$result = ClassRegistry::init($userModel)->find('first', array(
			'conditions' => $conditions,
			'recursive' => (int)$this->settings['recursive'],
			'contain' => $this->settings['contain'],
		));
		if (empty($result) || empty($result[$model])) {
			return false;
		}
		$user = $result[$model];
		unset($user[$fields['password']]);
		unset($result[$model]);
		return array_merge($user, $result);
	}

}
