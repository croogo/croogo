<?php

App::uses('AppHelper', 'View/Helper');

/**
 * @package Croogo.Croogo.View.Helper
 * @link http://bakery.cakephp.org/articles/view/recaptcha-component-helper-for-cakephp
 */
class RecaptchaHelper extends AppHelper {
	public $helpers = array('Form');

	public function display_form($output_method = 'return', $error = null, $use_ssl = false) {
		$this->Form->unlockField('recaptcha_challenge_field');
		$this->Form->unlockField('recaptcha_response_field');
		$data = $this->__form(Configure::read("Recaptcha.pubKey"),$error,$use_ssl);
		if ($output_method == "echo")
			echo $data;
		else
			return $data;
	}

	public function hide_mail($email = '',$output_method = 'return') {
		$data = $this->recaptcha_mailhide_html(Configure::read('Recaptcha.pubKey'), Configure::read('Recaptcha.privateKey'), $email);
		if ($output_method == "echo")
			echo $data;
		else
			return $data;
	}

	/**
	 * Gets the challenge HTML (javascript and non-javascript version).
	 * This is called from the browser, and the resulting reCAPTCHA HTML widget
	 * is embedded within the HTML form it was called from.
	 * @param string $pubkey A public key for reCAPTCHA
	 * @param string $error The error given by reCAPTCHA (optional, default is null)
	 * @param boolean $use_ssl Should the request be made over ssl? (optional, default is false)
	 * @return string - The HTML to be embedded in the user's form.
	 */
	private function __form($pubkey, $error = null, $use_ssl = false) {
		if ($pubkey == null || $pubkey == '') {
			die ("To use reCAPTCHA you must get an API key from <a href='http://recaptcha.net/api/getkey'>http://recaptcha.net/api/getkey</a>");
		}

		if ($use_ssl) {
			$server = Configure::read('Recaptcha.apiSecureServer');
		} else {
			$server = Configure::read('Recaptcha.apiServer');
		}

		$errorpart = "";
		if ($error) {
		   $errorpart = "&amp;error=" . $error;
		}
		return '<script type="text/javascript" src="'. $server . '/challenge?k=' . $pubkey . $errorpart . '"></script>

		<noscript>
			  <iframe src="'. $server . '/noscript?k=' . $pubkey . $errorpart . '" height="300" width="500" frameborder="0"></iframe><br/>
				  <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
				<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
			  <input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
		</noscript>';
	}

	/* Mailhide related code */
	protected function _recaptcha_aes_encrypt($val,$ky) {
		if (! function_exists ("mcrypt_encrypt")) {
			die ("To use reCAPTCHA Mailhide, you need to have the mcrypt php module installed.");
		}
		$mode=MCRYPT_MODE_CBC;
		$enc=MCRYPT_RIJNDAEL_128;
		$val=$this->_recaptcha_aes_pad($val);
		return mcrypt_encrypt($enc, $ky, $val, $mode, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
	}

	protected function _recaptcha_mailhide_urlbase64($x) {
		return strtr(base64_encode ($x), '+/', '-_');
	}

	/* gets the reCAPTCHA Mailhide url for a given email, public key and private key */
	public function recaptcha_mailhide_url($pubkey, $privkey, $email) {
		if ($pubkey == '' || $pubkey == null || $privkey == "" || $privkey == null) {
			die ("To use reCAPTCHA Mailhide, you have to sign up for a public and private key, " .
				 "you can do so at <a href='http://mailhide.recaptcha.net/apikey'>http://mailhide.recaptcha.net/apikey</a>");
		}


		$ky = pack('H*', $privkey);
		$cryptmail = $this->_recaptcha_aes_encrypt ($email, $ky);

		return "http://mailhide.recaptcha.net/d?k=" . $pubkey . "&c=" . $this->_recaptcha_mailhide_urlbase64 ($cryptmail);
	}

	/**
	 * gets the parts of the email to expose to the user.
	 * eg, given johndoe@example,com return ["john", "example.com"].
	 * the email is then displayed as john...@example.com
	 */
	protected function _recaptcha_mailhide_email_parts($email) {
		$arr = preg_split("/@/", $email );

		if (strlen ($arr[0]) <= 4) {
			$arr[0] = substr ($arr[0], 0, 1);
		} elseif (strlen ($arr[0]) <= 6) {
			$arr[0] = substr ($arr[0], 0, 3);
		} else {
			$arr[0] = substr ($arr[0], 0, 4);
		}
		return $arr;
	}

	/**
	 * Gets html to display an email address given a public an private key.
	 * to get a key, go to:
	 *
	 * http://mailhide.recaptcha.net/apikey
	 */
	public function recaptcha_mailhide_html($pubkey, $privkey, $email) {
		$emailparts = $this->_recaptcha_mailhide_email_parts ($email);
		$url = $this->recaptcha_mailhide_url ($pubkey, $privkey, $email);

		return htmlentities($emailparts[0]) . "<a href='" . htmlentities ($url) .
			"' onclick=\"window.open('" . htmlentities ($url) . "', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;\" title=\"Reveal this e-mail address\">...</a>@" . htmlentities ($emailparts [1]);

	}

}
