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

	public $privatekey = '';

	public $isValid = false;

	public $error = "";

	protected $_controller = null;

	public function startup(Controller $controller) {
		$this->publickey = Configure::read('Service.recaptcha_public_key');
		$this->privatekey = Configure::read('Service.recaptcha_private_key');

		Configure::write("Recaptcha.apiServer","http://api.recaptcha.net");
		Configure::write("Recaptcha.apiSecureServer","https://api-secure.recaptcha.net");
		Configure::write("Recaptcha.verifyServer","api-verify.recaptcha.net");
		Configure::write("Recaptcha.pubKey", $this->publickey);
		Configure::write("Recaptcha.privateKey", $this->privatekey);

		$this->_controller = $controller;
		$this->_controller->helpers[] = 'Croogo.Recaptcha';
	}

	public function valid($request) {
		if (isset($request->data['recaptcha_challenge_field']) && isset($request->data['recaptcha_response_field'])) {
			if ($this->recaptchaCheckAnswer(
				$this->privatekey,
				$_SERVER["REMOTE_ADDR"],
				$request->data['recaptcha_challenge_field'],
				$request->data['recaptcha_response_field']
			) == 0) {
				return false;
			}

			if ($this->isValid) {
				return true;
			}
		}
		return false;
	}

/**
	* Calls an HTTP POST function to verify if the user's guess was correct
	* @param string $privkey
	* @param string $remoteip
	* @param string $challenge
	* @param string $response
	* @param array $extraParams an array of extra variables to post to the server
	* @return ReCaptchaResponse
	*/
	public function recaptchaCheckAnswer($privkey, $remoteip, $challenge, $response, $extraParams = array()) {
		if ($privkey == null || $privkey == '') {
			die ("To use reCAPTCHA you must get an API key from <a href='http://recaptcha.net/api/getkey'>http://recaptcha.net/api/getkey</a>");
		}

		if ($remoteip == null || $remoteip == '') {
			die ("For security reasons, you must pass the remote ip to reCAPTCHA");
		}

		//discard spam submissions
		if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0) {
			$this->isValid = false;
			$this->error = 'incorrect-captcha-sol';
			return 0;
		}

		$response = $this->_recaptchaHttpPost(
			Configure::read('Recaptcha.verifyServer'), "/verify",
			array (
				'privatekey' => $privkey,
				'remoteip' => $remoteip,
				'challenge' => $challenge,
				'response' => $response
			) + $extraParams
		);

		$answers = explode("\n", $response[1]);

		if (trim($answers[0]) == 'true') {
			$this->isValid = true;
			return 1;
		} else {
			$this->isValid = false;
			$this->error = $answers[1];
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
	protected function _recaptchaHttpPost($host, $path, $data, $port = 80) {
		$req = $this->_recaptchaQsencode($data);

		$httpRequest = "POST $path HTTP/1.0\r\n";
		$httpRequest .= "Host: $host\r\n";
		$httpRequest .= "Content-Type: application/x-www-form-urlencoded;\r\n";
		$httpRequest .= "Content-Length: " . strlen($req) . "\r\n";
		$httpRequest .= "User-Agent: reCAPTCHA/PHP\r\n";
		$httpRequest .= "\r\n";
		$httpRequest .= $req;

		$response = '';
		if (false == ($fs = @fsockopen($host, $port, $errno, $errstr, 10))) {
			die ('Could not open socket');
		}

		fwrite($fs, $httpRequest);

		while (!feof($fs)) {
			$response .= fgets($fs, 1160); // One TCP-IP packet
		}
		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);

		return $response;
	}

/**
 * Encodes the given data into a query string format
 * @param $data - array of string elements to be encoded
 * @return string - encoded request
 */
	protected function _recaptchaQsencode($data) {
		$req = "";
		foreach ($data as $key => $value) {
			$req .= $key . '=' . urlencode(stripslashes($value)) . '&';
		}

		// Cut the last '&'
		$req = substr($req, 0, strlen($req) - 1);
		return $req;
	}
}
