<?php

App::uses('CakeEmail', 'Network/Email');
App::uses('UsersAppController', 'Users.Controller');

/**
 * Users Controller
 *
 * @category Controller
 * @package  Croogo.Users.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class UsersController extends UsersAppController {

/**
 * Components
 *
 * @var array
 * @access public
 */
	public $components = array(
		'Search.Prg' => array(
			'presetForm' => array(
				'paramType' => 'querystring',
			),
			'commonProcess' => array(
				'paramType' => 'querystring',
				'filterEmpty' => true,
			),
		),
	);

/**
 * Preset Variables Search
 *
 * @var array
 * @access public
 */
	public $presetVars = true;

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array('Users.User');

/**
 * implementedEvents
 *
 * @return array
 */
	public function implementedEvents() {
		return parent::implementedEvents() + array(
			'Controller.Users.beforeAdminLogin' => 'onBeforeAdminLogin',
			'Controller.Users.adminLoginFailure' => 'onAdminLoginFailure',
		);
	}

/**
 * Notify user when failed_login_limit hash been hit
 *
 * @return bool
 */
	public function onBeforeAdminLogin() {
		$field = $this->Auth->authenticate['all']['fields']['username'];
		if (empty($this->request->data)) {
			return true;
		}
		$cacheName = 'auth_failed_' . $this->request->data['User'][$field];
		$cacheValue = Cache::read($cacheName, 'users_login');
		if ($cacheValue >= Configure::read('User.failed_login_limit')) {
			$this->Session->setFlash(__d('croogo', 'You have reached maximum limit for failed login attempts. Please try again after a few minutes.'), 'default', array('class' => 'error'));
			return $this->redirect(array('action' => $this->request->params['action']));
		}
		return true;
	}

/**
 * Record the number of times a user has failed authentication in cache
 *
 * @return bool
 * @access public
 */
	public function onAdminLoginFailure() {
		$field = $this->Auth->authenticate['all']['fields']['username'];
		if (empty($this->request->data)) {
			return true;
		}
		$cacheName = 'auth_failed_' . $this->request->data['User'][$field];
		$cacheValue = Cache::read($cacheName, 'users_login');
		Cache::write($cacheName, (int)$cacheValue + 1, 'users_login');
		return true;
	}

/**
 * Admin index
 *
 * @return void
 * @access public
 * $searchField : Identify fields for search
 */
	public function admin_index() {
		$this->set('title_for_layout', __d('croogo', 'Users'));
		$this->Prg->commonProcess();
		$searchFields = array('role_id', 'name');

		$this->User->recursive = 0;
		$criteria = $this->User->parseCriteria($this->Prg->parsedParams());
		$this->paginate['conditions'] = $criteria;

		$this->set('users', $this->paginate());
		$this->set('roles', $this->User->Role->find('list'));
		$this->set('displayFields', $this->User->displayFields());
		$this->set('searchFields', $searchFields);

		if (isset($this->request->query['chooser'])) {
			$this->layout = 'admin_popup';
		}
	}

/**
 * Send activation email
 */
	private function __sendActivationEmail() {
		if (empty($this->request->data['User']['notification'])) {
			return;
		}

		$user = $this->request->data['User'];
		$activationUrl = Router::url(array(
			'admin' => false,
			'plugin' => 'users',
			'controller' => 'users',
			'action' => 'activate',
			$user['username'],
			$user['activation_key'],
		), true);
		$this->_sendEmail(
			array(Configure::read('Site.title'), $this->_getSenderEmail()),
			$user['email'],
			__d('croogo', '[%s] Please activate your account', Configure::read('Site.title')),
			'Users.register',
			'user activation',
			$this->theme,
			array(
				'user' => $this->request->data,
				'url' => $activationUrl,
			)
		);
	}

/**
 * Admin add
 *
 * @return void
 * @access public
 */
	public function admin_add() {
		if (!empty($this->request->data)) {
			$this->User->create();
			$this->request->data['User']['activation_key'] = md5(uniqid());
			if ($this->User->save($this->request->data)) {
				$this->request->data['User']['id'] = $this->User->id;
				$this->__sendActivationEmail();

				$this->Session->setFlash(__d('croogo', 'The User has been saved'), 'default', array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__d('croogo', 'The User could not be saved. Please, try again.'), 'default', array('class' => 'error'));
				unset($this->request->data['User']['password']);
			}
		} else {
			$this->request->data['User']['role_id'] = 2; // default Role: Registered
		}
		$roles = $this->User->Role->find('list');
		$this->set(compact('roles'));
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_edit($id = null) {
		if (!empty($this->request->data)) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The User has been saved'), 'default', array('class' => 'success'));
				return $this->Croogo->redirect(array('action' => 'edit', $this->User->id));
			} else {
				$this->Session->setFlash(__d('croogo', 'The User could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
		$roles = $this->User->Role->find('list');
		$this->set(compact('roles'));
		$this->set('editFields', $this->User->editFields());
	}

/**
 * Admin reset password
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_reset_password($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__d('croogo', 'Invalid User'), 'default', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'Password has been reset.'), 'default', array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__d('croogo', 'Password could not be reset. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
		$this->request->data = $this->User->findById($id);
	}

/**
 * Admin delete
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for User'), 'default', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}
		if ($this->User->delete($id)) {
			$this->Session->setFlash(__d('croogo', 'User deleted'), 'default', array('class' => 'success'));
			return $this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash(__d('croogo', 'User cannot be deleted'), 'default', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}
	}

/**
 * Admin login
 *
 * @return void
 * @access public
 */
	public function admin_login() {
		$this->set('title_for_layout', __d('croogo', 'Admin Login'));
		$this->layout = "admin_login";
		if ($this->Auth->user('id')) {
			if (!$this->Session->check('Message.auth')) {
				$this->Session->setFlash(
					__d('croogo', 'You are already logged in'), 'default',
					array('class' => 'alert'), 'auth'
				);
			}
			return $this->redirect($this->Auth->redirect());
		}
		if ($this->request->is('post')) {
			Croogo::dispatchEvent('Controller.Users.beforeAdminLogin', $this);
			if ($this->Auth->login()) {
				Croogo::dispatchEvent('Controller.Users.adminLoginSuccessful', $this);
				return $this->redirect($this->Auth->redirect());
			} else {
				Croogo::dispatchEvent('Controller.Users.adminLoginFailure', $this);
				$this->Auth->authError = __d('croogo', 'Incorrect username or password');
				$this->Session->setFlash($this->Auth->authError, 'default', array('class' => 'error'), 'auth');
				return $this->redirect($this->Auth->loginAction);
			}
		}
	}

/**
 * Admin logout
 *
 * @return void
 * @access public
 */
	public function admin_logout() {
		Croogo::dispatchEvent('Controller.Users.adminLogoutSuccessful', $this);
		$this->Session->setFlash(__d('croogo', 'Log out successful.'), 'default', array('class' => 'success'));
		return $this->redirect($this->Auth->logout());
	}

/**
 * Index
 *
 * @return void
 * @access public
 */
	public function index() {
		$this->set('title_for_layout', __d('croogo', 'Users'));
	}

/**
 * Convenience method to send email
 *
 * @param string $from Sender email
 * @param string $to Receiver email
 * @param string $subject Subject
 * @param string $template Template to use
 * @param string $theme Theme to use
 * @param array  $viewVars Vars to use inside template
 * @param string $emailType user activation, reset password, used in log message when failing.
 * @return boolean True if email was sent, False otherwise.
 */
	protected function _sendEmail($from, $to, $subject, $template, $emailType, $theme = null, $viewVars = null) {
		if (is_null($theme)) {
			$theme = $this->theme;
		}
		$success = false;

		try {
			$email = new CakeEmail();
			$email->from($from[1], $from[0]);
			$email->to($to);
			$email->subject($subject);
			$email->template($template);
			$email->viewVars($viewVars);
			$email->theme($theme);
			$success = $email->send();
		} catch (SocketException $e) {
			$this->log(sprintf('Error sending %s notification : %s', $emailType, $e->getMessage()));
		}

		return $success;
	}

/**
 * Add
 *
 * @return void
 * @access public
 */
	public function add() {
		$this->set('title_for_layout', __d('croogo', 'Register'));
		if (!empty($this->request->data)) {
			$this->User->create();
			$this->request->data['User']['role_id'] = 2; // Registered
			$this->request->data['User']['activation_key'] = md5(uniqid());
			$this->request->data['User']['status'] = 0;
			$this->request->data['User']['username'] = htmlspecialchars($this->request->data['User']['username']);
			$this->request->data['User']['website'] = htmlspecialchars($this->request->data['User']['website']);
			$this->request->data['User']['name'] = htmlspecialchars($this->request->data['User']['name']);
			if ($this->User->save($this->request->data)) {
				Croogo::dispatchEvent('Controller.Users.registrationSuccessful', $this);
				$this->request->data['User']['password'] = null;

				$this->_sendEmail(
					array(Configure::read('Site.title'), $this->_getSenderEmail()),
					$this->request->data['User']['email'],
					__d('croogo', '[%s] Please activate your account', Configure::read('Site.title')),
					'Users.register',
					'user activation',
					$this->theme,
					array('user' => $this->request->data)
				);

				$this->Session->setFlash(__d('croogo', 'You have successfully registered an account. An email has been sent with further instructions.'), 'default', array('class' => 'success'));
				return $this->redirect(array('action' => 'login'));
			} else {
				Croogo::dispatchEvent('Controller.Users.registrationFailure', $this);
				$this->Session->setFlash(__d('croogo', 'The User could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
	}

/**
 * Activate
 *
 * @param string $username
 * @param string $key
 * @return void
 * @access public
 */
	public function activate($username = null, $key = null) {
		if ($username == null || $key == null) {
			return $this->redirect(array('action' => 'login'));
		}

		if ($this->Auth->user('id')) {
			$this->Session->setFlash(
				__d('croogo', 'You are currently logged in as:') . ' ' .
				$this->Auth->user('username')
			);
			return $this->redirect($this->referer());
		}

		$redirect = array('action' => 'login');
		if (
			$this->User->hasAny(array(
				'User.username' => $username,
				'User.activation_key' => $key,
				'User.status' => 0,
			))
		) {
			$user = $this->User->findByUsername($username);
			$this->User->id = $user['User']['id'];

			$db = $this->User->getDataSource();
			$key = md5(uniqid());
			$this->User->updateAll(array(
				$this->User->escapeField('status') => $db->value(1),
				$this->User->escapeField('activation_key') => $db->value($key),
			), array(
				$this->User->escapeField('id') => $this->User->id
			));

			if (isset($user) && empty($user['User']['password'])) {
				$redirect = array('action' => 'reset', $username, $key);
			}

			Croogo::dispatchEvent('Controller.Users.activationSuccessful', $this);
			$this->Session->setFlash(__d('croogo', 'Account activated successfully.'), 'default', array('class' => 'success'));
		} else {
			Croogo::dispatchEvent('Controller.Users.activationFailure', $this);
			$this->Session->setFlash(__d('croogo', 'An error occurred.'), 'default', array('class' => 'error'));
		}

		if ($redirect) {
			return $this->redirect($redirect);
		}
	}

/**
 * Edit
 *
 * @return void
 * @access public
 */
	public function edit() {
	}

/**
 * Forgot
 *
 * @return void
 * @access public
 */
	public function forgot() {
		$this->set('title_for_layout', __d('croogo', 'Forgot Password'));

		if (!empty($this->request->data) && isset($this->request->data['User']['username'])) {
			$user = $this->User->findByUsername($this->request->data['User']['username']);
			if (!isset($user['User']['id'])) {
				$this->Session->setFlash(__d('croogo', 'Invalid username.'), 'default', array('class' => 'error'));
				return $this->redirect(array('action' => 'login'));
			}

			$this->User->id = $user['User']['id'];
			$activationKey = md5(uniqid());
			$this->User->saveField('activation_key', $activationKey);
			$this->set(compact('user', 'activationKey'));

			$emailSent = $this->_sendEmail(
				array(Configure::read('Site.title'), $this->_getSenderEmail()),
				$user['User']['email'],
				__d('croogo', '[%s] Reset Password', Configure::read('Site.title')),
				'Users.forgot_password',
				'reset password',
				$this->theme,
				compact('user', 'activationKey')
			);

			if ($emailSent) {
				$this->Session->setFlash(__d('croogo', 'An email has been sent with instructions for resetting your password.'), 'default', array('class' => 'success'));
				return $this->redirect(array('action' => 'login'));
			} else {
				$this->Session->setFlash(__d('croogo', 'An error occurred. Please try again.'), 'default', array('class' => 'error'));
			}
		}
	}

/**
 * Reset
 *
 * @param string $username
 * @param string $key
 * @return void
 * @access public
 */
	public function reset($username = null, $key = null) {
		$this->set('title_for_layout', __d('croogo', 'Reset Password'));

		if ($username == null || $key == null) {
			$this->Session->setFlash(__d('croogo', 'An error occurred.'), 'default', array('class' => 'error'));
			return $this->redirect(array('action' => 'login'));
		}

		$user = $this->User->find('first', array(
			'conditions' => array(
				'User.username' => $username,
				'User.activation_key' => $key,
			),
		));
		if (!isset($user['User']['id'])) {
			$this->Session->setFlash(__d('croogo', 'An error occurred.'), 'default', array('class' => 'error'));
			return $this->redirect(array('action' => 'login'));
		}

		if (!empty($this->request->data) && isset($this->request->data['User']['password'])) {
			$this->User->id = $user['User']['id'];
			$user['User']['activation_key'] = md5(uniqid());
			$user['User']['password'] = $this->request->data['User']['password'];
			$user['User']['verify_password'] = $this->request->data['User']['verify_password'];
			$options = array('fieldList' => array('password', 'verify_password', 'activation_key'));
			if ($this->User->save($user['User'], $options)) {
				$this->Session->setFlash(__d('croogo', 'Your password has been reset successfully.'), 'default', array('class' => 'success'));
				return $this->redirect(array('action' => 'login'));
			} else {
				$this->Session->setFlash(__d('croogo', 'An error occurred. Please try again.'), 'default', array('class' => 'error'));
			}
		}

		$this->set(compact('user', 'username', 'key'));
	}

/**
 * Login
 *
 * @return boolean
 * @access public
 */
	public function login() {
		$this->set('title_for_layout', __d('croogo', 'Log in'));
		if ($this->request->is('post')) {
			Croogo::dispatchEvent('Controller.Users.beforeLogin', $this);
			if ($this->Auth->login()) {
				Croogo::dispatchEvent('Controller.Users.loginSuccessful', $this);
				return $this->redirect($this->Auth->redirect());
			} else {
				Croogo::dispatchEvent('Controller.Users.loginFailure', $this);
				$this->Session->setFlash($this->Auth->authError, 'default', array('class' => 'error'), 'auth');
				return $this->redirect($this->Auth->loginAction);
			}
		}
	}

/**
 * Logout
 *
 * @return void
 * @access public
 */
	public function logout() {
		Croogo::dispatchEvent('Controller.Users.beforeLogout', $this);
		$this->Session->setFlash(__d('croogo', 'Log out successful.'), 'default', array('class' => 'success'));
		$redirect = $this->Auth->logout();
		Croogo::dispatchEvent('Controller.Users.afterLogout', $this);
		return $this->redirect($redirect);
	}

/**
 * View
 *
 * @param string $username
 * @return void
 * @access public
 */
	public function view($username = null) {
		if ($username == null) {
			$username = $this->Auth->user('username');
		}
		$user = $this->User->findByUsername($username);
		if (!isset($user['User']['id'])) {
			$this->Session->setFlash(__d('croogo', 'Invalid User.'), 'default', array('class' => 'error'));
			return $this->redirect('/');
		}

		$this->set('title_for_layout', $user['User']['name']);
		$this->set(compact('user'));
	}

	protected function _getSenderEmail() {
		return 'croogo@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
	}

}
