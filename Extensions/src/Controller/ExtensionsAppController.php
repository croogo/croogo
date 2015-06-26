<?php

namespace Croogo\Extensions\Controller;

use Cake\Event\Event;
use Croogo\Core\Controller\CroogoAppController;

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
class ExtensionsAppController extends CroogoAppController {

	public $helpers = [
		'Croogo/Core.Croogo'
	];

/**
 * beforeFilter
 *
 * @return void
 */
	public function initialize() {
		parent::initialize();

		if (in_array($this->request->param('action'), array('admin_delete', 'admin_toggle', 'admin_activate'))) {
			$this->request->allowMethod('post');
		}
	}

}
