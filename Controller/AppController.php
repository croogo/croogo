<?php

App::uses('Controller', 'Controller');

/**
 * Application controller
 *
 * This file is the base controller of all other controllers
 *
 * PHP version 5
 *
 * @category Controllers
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AppController extends Controller {
/**
 * Components
 *
 * @var array
 * @access public
 */
    public $components = array(
        'Croogo',
        'Security',
        'Acl',
        'Auth',
        'Session',
        'RequestHandler',
    );
/**
 * Helpers
 *
 * @var array
 * @access public
 */
    public $helpers = array(
        'Html',
        'Form',
        'Session',
        'Text',
        'Js',
        'Time',
        'Layout',
        'Custom',
    );
/**
 * Models
 *
 * @var array
 * @access public
 */
    public $uses = array(
        'Block',
        'Link',
        'Setting',
        'Node',
    );

/**
 * Pagination
 */
    public $paginate = array(
        'limit' => 10,
        );

/**
 * Cache pagination results
 *
 * @var boolean
 * @access public
 */
    public $usePaginationCache = true;
/**
 * View
 *
 * @var string
 * @access public
 */
    public $viewClass = 'Theme';
/**
 * Theme
 *
 * @var string
 * @access public
 */
    public $theme;
/**
 * Constructor
 *
 * @access public
 */
    public function __construct($request = null, $response = null) {
        Croogo::applyHookProperties('Hook.controller_properties');
        parent::__construct($request, $response);
        if ($this->name == 'CakeError') {
            $this->_set(Router::getPaths());
            $this->request->params = Router::getParams();
            $this->constructClasses();
            $this->startupProcess();
        }
    }
/**
 * beforeFilter
 *
 * @return void
 */
    public function beforeFilter() {
        parent::beforeFilter();
        $aclFilterComponent = Configure::read('Site.acl_plugin') . 'Filter';
        if (empty($this->{$aclFilterComponent})) {
            throw new MissingComponentException(array('class' => $aclFilterComponent));
        }
        $this->{$aclFilterComponent}->auth();
        $this->RequestHandler->setContent('json', 'text/x-json');
        $this->Security->blackHoleCallback = '__securityError';

        if (isset($this->request->params['admin']) && $this->name != 'CakeError') {
            $this->layout = 'admin';
        }

        if ($this->RequestHandler->isAjax()) {
            $this->layout = 'ajax';
        }

        if (Configure::read('Site.theme') && !isset($this->request->params['admin'])) {
            $this->theme = Configure::read('Site.theme');
        } elseif (Configure::read('Site.admin_theme') && isset($this->request->params['admin'])) {
            $this->theme = Configure::read('Site.admin_theme');
        }

        if (!isset($this->request->params['admin']) && 
            Configure::read('Site.status') == 0) {
            $this->layout = 'maintenance';
            $this->set('title_for_layout', __('Site down for maintenance'));
            $this->render('../Elements/blank');
        }

        if (isset($this->request->params['locale'])) {
            Configure::write('Config.language', $this->request->params['locale']);
        }
    }
/**
 * blackHoleCallback for SecurityComponent
 *
 * @return void
 */
    public function __securityError($type) {
        switch ($type) {
        case 'auth':
            break;
        case 'csrf':
            break;
        case 'get':
            break;
        case 'post':
            break;
        case 'put':
            break;
        case 'delete':
            break;
        default:
            break;
        }
        $this->set(compact('type'));
        $this->render('../Errors/security');
    }

}
?>