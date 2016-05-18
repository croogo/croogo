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

	public $Controller = null;

	public $privateKey = '';

	public $actions = array();

	public $settings = array();


	protected $_defaults = array(
		'actions' => array()
	);

/**
 * Constructor
 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		$this->Controller = $collection->getController();
		$this->_defaults['modelClass'] = $this->Controller->modelClass;
		$this->settings = array_merge($this->_defaults, $settings);
		$this->actions = $this->settings['actions'];
		unset($this->settings['actions']);
	}

/**
 * initialize
 */
	public function initialize(Controller $controller, $settings = array()) {
		if ($controller->name === 'CakeError') {
			return;
		}
		$this->Controller = $controller;

		if (in_array($this->Controller->action, $this->actions)) {
			$this->Controller->Security->validatePost = false;
		}

		if (!isset($this->Controller->helpers['Croogo.Recaptcha'])) {
			$this->Controller->helpers[] = 'Croogo.Recaptcha';
		}
	}

/**
 * startup
 */
	public function startup(Controller $controller) {
		$this->publicKey = Configure::read('Service.recaptcha_public_key');
		$this->privateKey = Configure::read('Service.recaptcha_private_key');

		Configure::write('Recaptcha.pubKey', $this->publicKey);
		Configure::write('Recaptcha.privateKey', $this->privateKey);
	}

/**
 * verify reCAPTCHA
 */
	public function verify() {
		if (isset($this->Controller->request->data['g-recaptcha-response'])) {
			$captcha = $this->Controller->request->data['g-recaptcha-response'];
			$response = $this->_getApiResponse($captcha);

			if (!$response->success) {
				$this->Session->setFlash($this->_errorMsg($response->{'error-codes'}), 'flash', array('class' => 'error'));
				return false;
			}
			return true;
		}
	}

/**
 * Get reCAPTCHA response
 *
 * @return array Body of the reCAPTCHA response
 */
	protected function _getApiResponse($captcha) {
		$data = array(
			'secret' => $this->privateKey,
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
		switch($errorCodes) {
			case 'missing-input-secret':
				$msg = 'The secret parameter is missing.';
				break;
			case 'invaid-input-secret':
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
