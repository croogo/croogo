<?php

namespace Croogo\Extensions\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

/**
 * ExtensionsApp Controller
 *
 * @category Controller
 * @package  Croogo.Extensions.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExtensionsAppController extends AppController {

/**
 * beforeFilter
 *
 * @return void
 */
	public function initialize() {
		if (in_array($this->request->param('action'), array('admin_delete', 'admin_toggle', 'admin_activate'))) {
			$this->request->allowMethod('post');
		}
	}

}
