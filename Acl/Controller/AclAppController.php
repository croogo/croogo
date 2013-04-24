<?php

App::uses('AppController', 'Controller');

/**
 * AclApp Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo.Acl
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclAppController extends AppController {

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Security->requirePost('admin_delete', 'admin_toggle');
	}

}
