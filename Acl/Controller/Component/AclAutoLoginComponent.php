<?php

App::uses('Component', 'Controller');

/**
 * Provides "Remember me" feature (via CookieAuthenticate) by listening to
 * to Controller.Users.adminLoginSuccessful event and creating the appropriate
 * cookie.
 *
 * PHP version 5
 *
 * @category Component
 * @package  Croogo.Acl.Controller.Component
 * @since    1.5
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclAutoLoginComponent extends Component {

/**
 * components
 */
	public $components = array(
		'Cookie',
		'Auth',
	);

/**
 * Controller instance
 */
	protected $_Controller;

/**
 * User Model to use (retrieved from AuthComponent)
 */
	protected $_userModel;

/**
 * Field setting (retrieved from AuthComponent)
 */
	protected $_fields;

/**
 * Component startup
 */
	public function startup(Controller $controller) {
		$this->_Controller = $controller;
		$this->Cookie->name = 'CAL';
		$controller->getEventManager()->attach(
			array($this, 'onAdminLogoutSuccessful'),
			'Controller.Users.adminLogoutSuccessful'
		);

		// skip autologin when mcrypt is not available
		if (!function_exists('mcrypt_decrypt')) {
			return;
		}

		$this->Cookie->type('rijndael');

		$setting = $this->Auth->authenticate['all'];
		list(, $this->_userModel) = pluginSplit($setting['userModel']);
		$this->_fields = $setting['fields'];

		$controller->getEventManager()->attach(
			array($this, 'onAdminLoginSuccessful'),
			'Controller.Users.adminLoginSuccessful'
		);

		$skipActions = array('logout', 'admin_logout');
		if (!in_array($controller->request->params['action'], $skipActions) &&
			empty($controller->request->data)) {
			$this->_loginByCookie();
		}
	}

/**
 * Prepare cookie data based on request
 *
 * @return array cookie data
 */
	protected function _cookie($request) {
		$time = time();
		$username = $request->data[$this->_userModel][$this->_fields['username']];
		return array_merge(array(
			'hash' => $this->Auth->password($username . $time),
			'time' => $time,
		), $request->data);
	}

/**
 * Verify cookie data
 *
 * return array|boolean false when data is invalid
 */
	protected function _verify() {
		if (!$this->Cookie->check($this->_userModel)) {
			return array();
		}
		$data = $this->Cookie->read($this->_userModel);
		if (is_array($data) && !empty($data['time'])) {
			$hash = $data['hash'];
			$time = $data['time'];
			$username = $data[$this->_userModel][$this->_fields['username']];
			if ($hash === $this->Auth->password($username . $time)) {
				return $data;
			}
		}
		return false;
	}

/**
 * Automatically login when cookie exists and valid
 *
 * @return void
 * @throws UnauthorizedException
 */
	protected function _loginByCookie() {
		$Controller = $this->_Controller;
		$request = $Controller->request;
		$userId = $this->Auth->user('id');
		if (empty($userId)) {
			$request->data = $this->_verify();
			if ($request->data === false) {
				$this->Cookie->delete($this->_userModel);
				throw new UnauthorizedException('Invalid cookie');
			}
			if ($this->Auth->login()) {
				$Controller->Session->delete('Message.auth');
				return $Controller->redirect($this->Auth->redirectUrl());
			}
		}
	}

/**
 * onAdminLoginSuccessful
 *
 * @return bool
 */
	public function onAdminLoginSuccessful($event) {
		$request = $event->subject->request;
		$remember = !empty($request->data[$this->_userModel]['remember']);
		$expires = Configure::read('Access Control.autoLoginDuration');
		if (strtotime($expires) === false) {
			$expires = '+1 week';
		}
		if ($request->is('post') && $remember) {
			$data = $this->_cookie($request);
			$this->Cookie->write($this->_userModel, $data, true, $expires);
		}
		return true;
	}

/**
 * onAdminLogoutSuccessful
 *
 * @return bool
 */
	public function onAdminLogoutSuccessful($event) {
		$this->Cookie->delete($this->_userModel);
		return true;
	}

}
