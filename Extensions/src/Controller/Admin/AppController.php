<?php

namespace Croogo\Extensions\Controller\Admin;

use Cake\Event\Event;
use Croogo\Core\Controller\Admin\AppController as CroogoController;

/**
 * Extensions Admin Controller
 *
 * @category Controller
 * @package  Croogo.Extensions.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AppController extends CroogoController
{
/**
 * beforeFilter
 *
 * @return void
 */
    public function initialize()
    {
        parent::initialize();

        if (in_array($this->getRequest()->getParam('action'), ['admin_delete', 'admin_toggle', 'admin_activate'])) {
            $this->getRequest()->allowMethod('post');
        }
    }
}
