<?php

namespace Croogo\Core\Controller;

use App\Controller\AppController as BaseController;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Routing\Router;

/**
 * Error Handling Controller
 *
 * Controller used by ErrorHandler to render error views.  This is based
 * on CakePHP's own CakeErrorController with the following differences:
 * - loads its own set of components and helpers
 * - aware of Site.theme and Site.admin_theme
 *
 * @category Controllers
 * @package  Croogo.Croogo.Controller
 * @version  1.0
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoErrorController extends BaseController
{

/**
 * Models
 *
 * @var array
 * @access public
 */
    public $uses = [];

/**
 * View
 *
 * @var string
 * @access public
 */
    public $viewClass = 'Theme';

/**
 * __construct
 *
 * @param Request $request
 * @param Response $response
 */
    public function __construct($request = null, $response = null)
    {
        parent::__construct($request, $response);
        if (count(Router::extensions()) && !isset($this->RequestHandler)
        ) {
            $this->loadComponent('RequestHandler');
        }
        $eventManager = $this->eventManager();
        if (isset($this->Auth)) {
            $eventManager->off($this->Auth);
        }
        if (isset($this->Security)) {
            $eventManager->off($this->Security);
        }
        $this->templatePath = 'Error';
    }

/**
 * beforeFilter
 *
 * @return void
 */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        if (Configure::read('Site.theme') && !isset($this->request->params['admin'])) {
            $this->theme = Configure::read('Site.theme');
        } elseif (isset($this->request->params['admin'])) {
            $adminTheme = Configure::read('Site.admin_theme');
            if ($adminTheme) {
                $this->theme = $adminTheme;
            }
            $this->layout = 'admin_full';
        }
    }

/**
 * Escapes the viewVars.
 *
 * @return void
 */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        foreach ($this->viewVars as $key => $value) {
            if (!is_object($value)) {
                $this->viewVars[$key] = h($value);
            }
        }
    }
}
