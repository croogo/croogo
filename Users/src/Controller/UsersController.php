<?php

namespace Croogo\Users\Controller;
use Cake\Network\Email\Email;
use Cake\Network\Exception\SocketException;
use Croogo\Croogo\Croogo;
use Cake\Core\Configure;

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
			$email = new Email();
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
					[Configure::read('Site.title'), $this->_getSenderEmail()],
					$this->request->data['User']['email'],
					__d('croogo', '[%s] Please activate your account', Configure::read('Site.title')),
					'Users.register',
					'user activation',
					$this->theme,
					['user' => $this->request->data]
				);

				$this->Session->setFlash(__d('croogo', 'You have successfully registered an account. An email has been sent with further instructions.'), 'default', ['class' => 'success']);
				return $this->redirect(['action' => 'login']);
			} else {
				Croogo::dispatchEvent('Controller.Users.registrationFailure', $this);
				$this->Session->setFlash(__d('croogo', 'The User could not be saved. Please, try again.'), 'default', ['class' => 'error']);
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
			return $this->redirect(['action' => 'login']);
		}

		if ($this->Auth->user('id')) {
			$this->Session->setFlash(
				__d('croogo', 'You are currently logged in as:') . ' ' .
				$this->Auth->user('username')
			);
			return $this->redirect($this->referer());
		}

		$redirect = ['action' => 'login'];
		if (
			$this->User->hasAny([
				'User.username' => $username,
				'User.activation_key' => $key,
				'User.status' => 0,
			])
		) {
			$user = $this->User->findByUsername($username);
			$this->User->id = $user['User']['id'];

			$db = $this->User->getDataSource();
			$key = md5(uniqid());
			$this->User->updateAll([
				$this->User->escapeField('status') => $db->value(1),
				$this->User->escapeField('activation_key') => $db->value($key),
			], [
				$this->User->escapeField('id') => $this->User->id
			]);

			if (isset($user) && empty($user['User']['password'])) {
				$redirect = ['action' => 'reset', $username, $key];
			}

			Croogo::dispatchEvent('Controller.Users.activationSuccessful', $this);
			$this->Session->setFlash(__d('croogo', 'Account activated successfully.'), 'default', ['class' => 'success']);
		} else {
			Croogo::dispatchEvent('Controller.Users.activationFailure', $this);
			$this->Session->setFlash(__d('croogo', 'An error occurred.'), 'default', ['class' => 'error']);
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
				$this->Session->setFlash(__d('croogo', 'Invalid username.'), 'default', ['class' => 'error']);
				return $this->redirect(['action' => 'login']);
			}

			$this->User->id = $user['User']['id'];
			$activationKey = md5(uniqid());
			$this->User->saveField('activation_key', $activationKey);
			$this->set(compact('user', 'activationKey'));

			$emailSent = $this->_sendEmail(
				[Configure::read('Site.title'), $this->_getSenderEmail()],
				$user['User']['email'],
				__d('croogo', '[%s] Reset Password', Configure::read('Site.title')),
				'Users.forgot_password',
				'reset password',
				$this->theme,
				compact('user', 'activationKey')
			);

			if ($emailSent) {
				$this->Session->setFlash(__d('croogo', 'An email has been sent with instructions for resetting your password.'), 'default', ['class' => 'success']);
				return $this->redirect(['action' => 'login']);
			} else {
				$this->Session->setFlash(__d('croogo', 'An error occurred. Please try again.'), 'default', ['class' => 'error']);
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
			$this->Session->setFlash(__d('croogo', 'An error occurred.'), 'default', ['class' => 'error']);
			return $this->redirect(['action' => 'login']);
		}

		$user = $this->User->find('first', [
			'conditions' => [
				'User.username' => $username,
				'User.activation_key' => $key,
			],
		]);
		if (!isset($user['User']['id'])) {
			$this->Session->setFlash(__d('croogo', 'An error occurred.'), 'default', ['class' => 'error']);
			return $this->redirect(['action' => 'login']);
		}

		if (!empty($this->request->data) && isset($this->request->data['User']['password'])) {
			$this->User->id = $user['User']['id'];
			$user['User']['activation_key'] = md5(uniqid());
			$user['User']['password'] = $this->request->data['User']['password'];
			$user['User']['verify_password'] = $this->request->data['User']['verify_password'];
			$options = [
				'fieldList' => [
					'password',
					'verify_password',
					'activation_key'
				]
			];
			if ($this->User->save($user['User'], $options)) {
				$this->Session->setFlash(__d('croogo', 'Your password has been reset successfully.'), 'default', ['class' => 'success']);
				return $this->redirect(['action' => 'login']);
			} else {
				$this->Session->setFlash(__d('croogo', 'An error occurred. Please try again.'), 'default', ['class' => 'error']);
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
			$user = $this->Auth->identify();
			if ($user) {
				$this->Auth->setUser($user);

				Croogo::dispatchEvent('Controller.Users.loginSuccessful', $this);
				return $this->redirect($this->Auth->redirectUrl());
			} else {
				Croogo::dispatchEvent('Controller.Users.loginFailure', $this);
				$this->Flash->error($this->Auth->authError);
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
		$this->Session->setFlash(__d('croogo', 'Log out successful.'), 'default', ['class' => 'success']);
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
			$this->Session->setFlash(__d('croogo', 'Invalid User.'), 'default', ['class' => 'error']);
			return $this->redirect('/');
		}

		$this->set('title_for_layout', $user['User']['name']);
		$this->set(compact('user'));
	}

	protected function _getSenderEmail() {
		return 'croogo@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
	}

}
