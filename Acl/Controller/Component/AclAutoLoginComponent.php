<?php

namespace Croogo\Acl\Controller\Component;

use Cake\Controller\Component;
/**
 * Provides "Remember me" feature (via CookieAuthenticate) by listening to
 * to Controller.Users.adminLoginSuccessful event and creating the appropriate
 * cookie.
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
 * Constructor
 */
	public function __construct(ComponentRegistry $collection, $settings = array()) {
		$settings = Hash::merge(array(
			'cookieName' => 'CAL',
		), $settings);
		return parent::__construct($collection, $settings);
	}

/**
 * Component startup
 */
	public function startup(Controller $controller) {
		$this->_Controller = $controller;
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
	}

/**
 * Prepare cookie data based on request
 *
 * @return array cookie data
 */
	protected function _cookie($request) {
		$time = time();
		$username = $request->data[$this->_userModel][$this->_fields['username']];
		$data = json_encode(array(
			'hash' => $this->Auth->password($username . $time),
			'time' => $time,
			'username' => $username,
		));

		$mac = hash_hmac('sha256', $data, Configure::read('Security.salt'));
		return compact('mac', 'data');
	}

/**
 * Helper method to write autologin cookie
 *
 * @see CookieComponent::write()
 * @return void
 */
	protected function _writeCookie($key, $value = null, $encrypt = true, $expires = null) {
		$this->Cookie->name = $this->settings['cookieName'];
		$this->Cookie->write($key, $value, $encrypt, $expires);
	}

/**
 * Helper method to read autologin cookie
 *
 * @see CookieComponent::read()
 * @return string or null, value for specified key
 */
	protected function _readCookie($key = null) {
		$this->Cookie->name = $this->settings['cookieName'];
		return $this->Cookie->read($key);
	}

/**
 * Helper method to check autologin cookie
 *
 * @see CookieComponent::check()
 * @return boolean True if variable is there
 */
	protected function _checkCookie($key) {
		$this->Cookie->name = $this->settings['cookieName'];
		return $this->Cookie->check($key);
	}

/**
 * Helper method to delete autologin cookie
 *
 * @see CookieComponent::delete()
 * @return void
 */
	protected function _deleteCookie($key) {
		$this->Cookie->name = $this->settings['cookieName'];
		$this->Cookie->delete($key);
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
			$this->_writeCookie($this->_userModel, $data, true, $expires);
		}
		return true;
	}

/**
 * onAdminLogoutSuccessful
 *
 * @return bool
 */
	public function onAdminLogoutSuccessful($event) {
		$this->_deleteCookie($this->_userModel);
		return true;
	}

}
