<?php

App::uses('ExampleAppController', 'Example.Controller');

/**
 * Example Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExampleController extends ExampleAppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'Example';

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array('Setting');

/**
 * admin_index
 *
 * @return void
 */
	public function admin_index() {
		$this->set('title_for_layout', __d('croogo', 'Example'));
	}

/**
 * admin_chooser
 *
 * @return void
 */
	public function admin_chooser() {
		$this->set('title_for_layout', __d('croogo', 'Chooser Example'));
	}

/**
 * index
 *
 * @return void
 */
	public function index() {
		$this->set('title_for_layout', __d('croogo', 'Example'));
		$this->set('exampleVariable', 'value here');
	}

	public function admin_add() {
	}

}
