<?php
namespace Croogo\Acl\Controller\Component\Auth;
App::uses('BaseAuthenticate', 'Controller/Component/Auth');
App::uses('AuthComponent', 'Controller/Component');
App::uses('Router', 'Routing');

/**
 * An authentication adapter for AuthComponent.  Provides the ability to authenticate using COOKIE
 *
 * {{{
 *	$this->Auth->authenticate = array(
 *		'Authenticate.Cookie' => array(
 *			'fields' => array(
 *				'username' => 'username',
 *				'password' => 'password'
 *			),
 *			'userModel' => 'User',
 *			'scope' => array('User.active' => 1),
 *			'crypt' => 'rijndael', // Defaults to rijndael(safest), optionally set to 'cipher' if required
 *			'cookie' => array(
 *				'name' => 'RememberMe',
 *				'time' => '+2 weeks',
 *			)
 *		)
 *	)
 * }}}
 *
 * @package     Croogo.Acl.Controller.Component.Auth
 * @copyright   Copyright (c) 2012 Ceeram
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @see AclAutoLoginComponent
 */
class CookieAuthenticate extends BaseAuthenticate {

/**
 * Constructor
 */
	public function __construct(ComponentRegistry $collection, $settings) {
		$this->settings['cookie'] = array(
			'name' => 'CAL',
			'time' => '+2 weeks',
		);
		$this->settings['crypt'] = 'rijndael';
		parent::__construct($collection, $settings);
	}

/**
 * Verify cookie data
 *
 * return boolean|array User data or boolean False when data is invalid
 */
	protected function _verify($cookie) {
		if (empty($cookie['data'])) {
			return false;
		}

		$data = $cookie['data'];
		$mac = hash_hmac('sha256', $data, Configure::read('Security.salt'));
		if ($mac !== $cookie['mac']) {
			return false;
		}

		$data = json_decode($cookie['data'], true);
		$settings = $this->settings;
		$fields = $settings['fields'];
		if (empty($data['hash']) ||
			empty($data['time']) ||
			empty($data[$fields['username']])
		) {
			return false;
		}

		$username = $data[$fields['username']] . $data['time'];
		if ($data['hash'] === $this->_registry->Auth->password($username)) {
			return $data;
		}

		return false;
	}

/**
 * Authenticates the identity contained in the cookie.  Will use the `settings.userModel`, and `settings.fields`
 * to find COOKIE data that is used to find a matching record in the `settings.userModel`.  Will return false if
 * there is no cookie data, either username or password is missing, of if the scope conditions have not been met.
 *
 * @param CakeRequest $request The unused request object
 * @return mixed False on login failure. An array of User data on success.
 * @throws CakeException
 */
	public function getUser(CakeRequest $request) {
		if (!isset($this->_registry->Cookie) || !$this->_registry->Cookie instanceof CookieComponent) {
			throw new CakeException('CookieComponent is not loaded');
		}

		$this->settings = array_merge(array('crypt' => 'rijndael'), $this->settings);
		if ($this->settings['crypt'] == 'rijndael' && !function_exists('mcrypt_encrypt')) {
			throw new CakeException('Cannot use type rijndael, mcrypt_encrypt() is required');
		}
		$this->_registry->Cookie->type($this->settings['crypt']);

		list(, $model) = pluginSplit($this->settings['userModel']);

		$this->_registry->Cookie->name = $this->settings['cookie']['name'];
		$cookie = $this->_registry->Cookie->read($model);
		$data = $this->_verify($cookie);
		if (!$data) {
			return false;
		}

		extract($this->settings['fields']);
		if (empty($data[$username])) {
			return false;
		}

		$user = $this->_findUser($data[$username]);
		if ($user) {
			$this->_registry->Session->write(AuthComponent::$sessionKey, $user);
			return $user;
		}
		return false;
	}

/**
 * Find a user record
 *
 * @see BaseAuthenticate::_findUser()
 */
	protected function _findUser($conditions, $password = null) {
		$userModel = $this->settings['userModel'];
		list(, $model) = pluginSplit($userModel);
		$fields = $this->settings['fields'];

		if (!is_array($conditions)) {
			$username = $conditions;
			$conditions = array(
				$model . '.' . $fields['username'] => $username,
			);
		}
		if (!empty($this->settings['scope'])) {
			$conditions = array_merge($conditions, $this->settings['scope']);
		}
		$result = ClassRegistry::init($userModel)->find('first', array(
			'conditions' => $conditions,
			'recursive' => $this->settings['recursive'],
			'contain' => $this->settings['contain'],
		));
		if (empty($result) || empty($result[$model])) {
			return false;
		}
		$user = $result[$model];
		if (
			isset($conditions[$model . '.' . $fields['password']]) ||
			isset($conditions[$fields['password']])
		) {
			unset($user[$fields['password']]);
		}
		unset($result[$model]);
		return array_merge($user, $result);
	}

/**
 * Authenticate a user based on the request information
 *
 * @see BaseAuthenticate::authenticate()
 */
	public function authenticate(CakeRequest $request, CakeResponse $response) {
		if (!empty($request->data) || $request->is('post')) {
			return false;
		}
		return $this->getUser($request);
	}

/**
 * Logout
 */
	public function logout($user) {
		$this->_registry->Cookie->destroy();
	}

}
