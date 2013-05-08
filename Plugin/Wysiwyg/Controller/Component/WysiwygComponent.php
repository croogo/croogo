<?php

App::uses('Component', 'Controller');

/**
 * Wysiwyg Component
 *
 * PHP version 5
 *
 * @category Wysiwyg.Controller.Component
 * @package  Croogo.Wysiwyg.Controller.Component
 * @version  1.5
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class WysiwygComponent extends Component {

/**
 * Key name used for storing redirect information in sessions
 */
	protected $_key = 'Wysiwyg.redirect';

/**
 * List of actions that we are interested in
 */
	protected $_actions = array('admin_add', 'admin_edit', 'admin_delete');

/**
 * Store the referer information for use later
 */
	public function startup(Controller $controller) {
		$redirect = $controller->Session->read($this->_key);
		if (!in_array($controller->action, $this->_actions)) {
			return;
		}
		if (!empty($redirect)) {
			return;
		}
		$controller->Session->write($this->_key, $controller->referer());
	}

/**
 * Replace the redirect $url when appropriate
 */
	public function beforeRedirect(Controller $controller, $url, $status = null, $exit = true) {
		$redirect = $controller->Session->read($this->_key);
		if (!empty($redirect)) {
			if (in_array($controller->action, $this->_actions)) {
				$controller->Session->delete($this->_key);
			}
			return $redirect;
		}
		return $url;
	}

}
