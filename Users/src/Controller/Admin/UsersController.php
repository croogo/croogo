<?php

namespace Croogo\Users\Controller\Admin;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Network\Email\Email;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use Croogo\Core\Controller\CroogoAppController;
use Croogo\Core\Croogo;
use Croogo\Users\Model\Entity\User;

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
class UsersController extends CroogoAppController {

/**
 * Initialize
 *
 * @return void
 */
	public function initialize() {
		parent::initialize();

		$this->loadComponent('Search.Prg', [
			'presetForm' => [
				'paramType' => 'querystring',
			],
			'commonProcess' => [
				'paramType' => 'querystring',
				'filterEmpty' => true,
			],
		]);
	}

/**
 * implementedEvents
 *
 * @return array
 */
	public function implementedEvents() {
		return parent::implementedEvents() + [
			'Controller.Users.beforeAdminLogin' => 'onBeforeAdminLogin',
			'Controller.Users.adminLoginFailure' => 'onAdminLoginFailure',
		];
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
		$cacheName = 'auth_failed_' . $this->request->data[$field];
		$cacheValue = Cache::read($cacheName, 'users_login');
		if ($cacheValue >= Configure::read('User.failed_login_limit')) {
			$this->Flash->error(__d('croogo', 'You have reached maximum limit for failed login attempts. Please try again after a few minutes.'));
			return $this->redirect(['action' => $this->request->param('action')]);
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
		$cacheName = 'auth_failed_' . $this->request->data[$field];
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
	public function index() {
		$this->Prg->commonProcess();
		$searchFields = ['role_id', 'name'];

		$this->paginate = [
			'contain' => [
				'Roles'
			]
		];

		$criteria = $this->Users->find('searchable', $this->Prg->parsedParams());

		$this->set('users', $this->paginate($criteria));
		$this->set('roles', $this->Users->Roles->find('list'));
		$this->set('displayFields', $this->Users->displayFields());
		$this->set('searchFields', $searchFields);

		if (isset($this->request->query['chooser'])) {
			$this->layout = 'admin_popup';
		}
	}


/**
 * Admin add
 *
 * @return void
 * @access public
 */
	public function add() {
		$user = $this->Users->newEntity();

		if ($this->request->is('post')) {
			$user = $this->Users->patchEntity($user, Hash::merge($this->request->data(), ['activation_key' => Text::uuid()]));

			if ($this->Users->save($user)) {

				if ($this->request->data('notification') != null) {
					$this->__sendActivationEmail($user);
				}

				$this->Flash->success(__d('croogo', 'The User has been saved'));

				if ($this->request->data('apply') === null) {
					return $this->redirect(['action' => 'index']);
				} else {
					return $this->redirect(['action' => 'edit', $user->id]);
				}
			} else {
				$this->Flash->error(__d('croogo', 'The User could not be saved. Please, try again.'));
			}
		}

		$this->set('user', $user);
		$this->set('roles', $this->Users->Roles->find('list'));
		$this->set('editFields', $this->Users->editFields());
	}

/**
 * Send activation email
 */
	private function __sendActivationEmail(User $user) {
		$url = Router::url([
			'prefix' => false,
			'plugin' => 'Croogo/Users',
			'controller' => 'Users',
			'action' => 'activate',
			$user->username,
			$user->activation_key,
		], true);


		$email = new Email('default');
		$email->from([$this->_getSenderEmail() => Configure::read('Site.title')])
			->to($user->email)
			->subject(__d('croogo', '[{0}] Please activate your account', Configure::read('Site.title')))
			->template('Croogo/Users.register')
			->viewVars(compact('url', 'user'))
			->send();
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function edit($id = null) {
		$user = $this->Users->get($id);

		if ($this->request->is('put')) {
			$this->Users->patchEntity($user, $this->request->data());

			if ($this->Users->save($user)) {
				$this->Flash->success(__d('croogo', 'The User has been saved'));

				if ($this->request->data('apply') === null) {
					return $this->redirect(['action' => 'index']);
				} else {
					return $this->redirect(['action' => 'edit', $user->id]);
				}
			} else {
				$this->Flash->error(__d('croogo', 'The User could not be saved. Please, try again.'));
			}
		}

		$this->set('user', $user);
		$this->set('roles', $this->Users->Roles->find('list'));
		$this->set('editFields', $this->Users->editFields());
	}

/**
 * Admin reset password
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function reset_password($id = null) {
		$user = $this->Users->get($id);

		if ($this->request->is('put')) {
			$user = $this->Users->patchEntity($user, $this->request->data());

			if ($this->Users->save($user)) {
				$this->Flash->success(__d('croogo', 'Password has been reset.'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__d('croogo', 'Password could not be reset. Please, try again.'));
			}
		}

		$this->set('user', $user);
	}

/**
 * Admin delete
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function delete($id = null) {
		$user = $this->Users->get($id);

		if ($this->Users->delete($user)) {
			$this->Flash->success(__d('croogo', 'User deleted'));
		} else {
			$this->Flash->error(__d('croogo', 'User cannot be deleted'));
		}
		return $this->redirect(['action' => 'index']);
	}

/**
 * Admin login
 *
 * @return void
 * @access public
 */
	public function login() {
		$this->layout = 'admin_login';

		if ($this->Auth->user('id')) {
			if (!$this->request->session()->check('Message.auth')) {
				$this->Flash->alert(__d('croogo', 'You are already logged in'), ['key' => 'auth']);
			}
			return $this->redirect($this->Auth->redirectUrl());
		}

		if ($this->request->is('post')) {
			Croogo::dispatchEvent('Controller.Users.beforeAdminLogin', $this);
			$user = $this->Auth->identify();
			if ($user) {
				$this->Auth->setUser($user);

				if ($this->Auth->authenticationProvider()->needsPasswordRehash()) {
					$user = $this->Users->get($user['id']);
					$user->password = $this->request->data('password');
					$this->Users->save($user);
				}

				Croogo::dispatchEvent('Controller.Users.adminLoginSuccessful', $this);
				return $this->redirect($this->Auth->redirectUrl());
			} else {
				Croogo::dispatchEvent('Controller.Users.adminLoginFailure', $this);
				$this->Auth->authError = __d('croogo', 'Incorrect username or password');
				$this->Flash->error($this->Auth->authError, ['key' => 'auth']);
				return $this->redirect($this->Auth->config('loginAction'));
			}
		}
	}

/**
 * Admin logout
 *
 * @return void
 * @access public
 */
	public function logout() {
		Croogo::dispatchEvent('Controller.Users.adminLogoutSuccessful', $this);
		$this->Flash->success(__d('croogo', 'Log out successful.'), ['key' => 'auth']);
		return $this->redirect($this->Auth->logout());
	}

	protected function _getSenderEmail() {
		return 'croogo@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
	}
}
