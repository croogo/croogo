<?php

App::uses('AppHelper', 'View/Helper');
/**
 * Recaptcha Helper
 *
 * @package Croogo.Croogo.View.Helper
 */
class RecaptchaHelper extends AppHelper {

/**
 * secure API Url
 */
	const SECURE_API_URL = 'https://www.google.com/recaptcha/api.js';

/**
 * helpers
 */
	public $helpers = array('Html', 'Form', 'Js');

/**
 * beforeRender
 */
	public function beforeRender($viewFile) {
		$params = $this->_View->params;
		if (isset($params['isAjax']) && $params['isAjax'] === true) {
			return;
		}
		if (isset($params['admin']) && $params['admin'] === true) {
			return;
		}
		$this->Html->script(self::SECURE_API_URL, array('inline' => false));
	}

/**
 * render
 * @data-type: audio | image
 */
	public function display($options = array()) {
		$_defaults = array(
			'data-sitekey' => Configure::read('Recaptcha.pubKey'),
		);
		$options = array_merge($_defaults, $options);

		$div = $this->Html->div('g-recaptcha', '&nbsp;', $options);
		return $div;
	}
}
