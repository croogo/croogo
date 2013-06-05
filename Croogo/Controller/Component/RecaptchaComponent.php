<?php

App::uses('Component', 'Controller');

/**
 * Recaptcha Component
 *
 * @package Croogo.Croogo.Controller.Component
 * @category Component
 * @link http://bakery.cakephp.org/articles/view/recaptcha-component-helper-for-cakephp
 */
class RecaptchaComponent extends Component {
	public $publickey = '';
	public $privatekey= '';

	public $is_valid = false;
	public $error = "";

	protected $controller = null;

	public function startup(Controller $controller) {
		$this->publickey = Configure::read('Service.recaptcha_public_key');
		$this->privatekey = Configure::read('Service.recaptcha_private_key');

		Configure::write("Recaptcha.apiServer","http://api.recaptcha.net");
		Configure::write("Recaptcha.apiSecureServer","https://api-secure.recaptcha.net");
		Configure::write("Recaptcha.verifyServer","api-verify.recaptcha.net");
		Configure::write("Recaptcha.pubKey", $this->publickey);
		Configure::write("Recaptcha.privateKey", $this->privatekey);

		$this->controller = $controller;
		$this->controller->helpers[] = 'Croogo.Recaptcha';
	}

	public function valid($request) {
		if (isset($request->data['recaptcha_challenge_field']) && isset($request->data['recaptcha_response_field'])) {
			if ($this->recaptcha_check_answer(
				$this->privatekey,
				$_SERVER["REMOTE_ADDR"],
				$request->data['recaptcha_challenge_field'],
				$request->data['recaptcha_response_field']
			) == 0)
				return false;

			if ($this->is_valid)
				return true;
		}
		return false;
	}

	/**
	  * Calls an HTTP POST function to verify if the user's guess was correct
	  * @param string $privkey
	  * @param string $remoteip
	  * @param string $challenge
	  * @param string $response
	  * @param array $extra_params an array of extra variables to post to the server
	  * @return ReCaptchaResponse
	  */
	public function recaptcha_check_answer($privkey, $remoteip, $challenge, $response, $extra_params = array()) {
		if ($privkey == null || $privkey == '') {
			die ("To use reCAPTCHA you must get an API key from <a href='http://recaptcha.net/api/getkey'>http://recaptcha.net/api/getkey</a>");
		}

		if ($remoteip == null || $remoteip == '') {
			die ("For security reasons, you must pass the remote ip to reCAPTCHA");
		}

			//discard spam submissions
			if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0) {
					$this->is_valid = false;
					$this->error = 'incorrect-captcha-sol';
					return 0;
			}

			$response = $this->_recaptcha_http_post(Configure::read('Recaptcha.verifyServer'), "/verify",
											  array (
													 'privatekey' => $privkey,
													 'remoteip' => $remoteip,
													 'challenge' => $challenge,
													 'response' => $response
													 ) + $extra_params
											  );

			$answers = explode ("\n", $response [1]);

			if (trim ($answers [0]) == 'true') {
					$this->is_valid = true;
					return 1;
			} else {
					$this->is_valid = false;
					$this->error = $answers [1];
					return 0;
			}
	}


	/**
	 * Submits an HTTP POST to a reCAPTCHA server
	 * @param string $host
	 * @param string $path
	 * @param array $data
	 * @param int port
	 * @return array response
	 */
	protected function _recaptcha_http_post($host, $path, $data, $port = 80) {

		$req = $this->_recaptcha_qsencode ($data);

		$http_request  = "POST $path HTTP/1.0\r\n";
		$http_request .= "Host: $host\r\n";
		$http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
		$http_request .= "Content-Length: " . strlen($req) . "\r\n";
		$http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
		$http_request .= "\r\n";
		$http_request .= $req;

		$response = '';
		if ( false == ( $fs = @fsockopen($host, $port, $errno, $errstr, 10) ) ) {
				die ('Could not open socket');
		}

		fwrite($fs, $http_request);

		while ( !feof($fs) )
				$response .= fgets($fs, 1160); // One TCP-IP packet
		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);

		return $response;
	}


	/**
	 * Encodes the given data into a query string format
	 * @param $data - array of string elements to be encoded
	 * @return string - encoded request
	 */
	protected function _recaptcha_qsencode($data) {
		$req = "";
		foreach ( $data as $key => $value )
				$req .= $key . '=' . urlencode( stripslashes($value) ) . '&';

		// Cut the last '&'
		$req=substr($req,0,strlen($req)-1);
		return $req;
	}
}
