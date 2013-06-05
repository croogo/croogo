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
		$this->set('title_for_layout', 'Example');
	}

/**
 * admin_chooser
 *
 * @return void
 */
	public function admin_chooser() {
		$this->set('title_for_layout', 'Chooser Example');
	}

/**
 * index
 *
 * @return void
 */
	public function index() {
		$this->set('title_for_layout', 'Example');
		$this->set('exampleVariable', 'value here');
	}

	public function admin_add() {
	}

	public function admin_rte_example() {
		$notice = 'If editors are not displayed correctly, check that `Ckeditor` plugin is loaded after `Example` plugin.';
		$this->Session->setFlash($notice, 'default', array('class' => 'success'));
	}

}
