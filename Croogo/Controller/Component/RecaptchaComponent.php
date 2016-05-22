<?php

App::uses('HttpSocket', 'Network/Http');
App::uses('Component', 'Controller');

/**
 * Recaptcha Component
 *
 * @package Croogo.Croogo.Controller.Component
 * @category Component
 */
class RecaptchaComponent extends Component {

	const SITE_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

	const VERSION = 'php_1.1.2';

	public $components = array('Session');

	protected $_publicKey = '';

	protected $_privateKey = '';

	protected $controller = null;

/**
 * initialize
 */
	public function initialize(Controller $controller, $settings = array()) {
		if ($controller->name === 'CakeError') {
			return;
		}
		$this->controller = $controller;

		if (!isset($this->controller->helpers['Croogo.Recaptcha'])) {
			$this->controller->helpers[] = 'Croogo.Recaptcha';
		}
	}

/**
 * startup
 */
	public function startup(Controller $controller) {
		$this->_publicKey = Configure::read('Service.recaptcha_public_key');
		$this->_privateKey = Configure::read('Service.recaptcha_private_key');
	}

/**
 * verify reCAPTCHA
 */
	public function valid($request = null) {
		if (!$request) {
			$request = $this->controller->request;
		}
		if (isset($request->data['g-recaptcha-response'])) {
			$captcha = $request->data['g-recaptcha-response'];
			$response = $this->_getApiResponse($captcha);

			if (!$response->success) {
				$this->Session->setFlash(
					$this->_errorMsg($response->{'error-codes'}),
					'flash',
					array('class' => 'error')
				);
				return false;
			}
			return true;
		}
		return false;
	}

/**
 * Get reCAPTCHA response
 *
 * @return array Body of the reCAPTCHA response
 */
	protected function _getApiResponse($captcha) {
		$data = array(
			'secret' => $this->_privateKey,
			'response' => $captcha,
			'remoteip' => env('REMOTE_ADDR'),
			'version' => self::VERSION
		);
		$HttpSocket = new HttpSocket();
		$request = $HttpSocket->post(self::SITE_VERIFY_URL, $data);
		return json_decode($request->body());
	}

/**
 * Error message
 */
	protected function _errorMsg($errorCodes = null) {
		switch ($errorCodes) {
			case 'missing-input-secret':
				$msg = 'The secret parameter is missing.';
				break;
			case 'invalid-input-secret':
				$msg = 'The secret parameter is invalid or malformed.';
				break;
			case 'missing-input-response':
				$msg = 'The response parameter is missing.';
				break;
			case 'invalid-input-response':
				$msg = 'The response parameter is invalid or malformed.';
				break;
			default:
				$msg = null;
				break;
		}
		return $msg;
	}

}
