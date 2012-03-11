<?php
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
		'Acl.AclFilter',
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
	public $view = 'Theme';

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
	public function __construct() {
		Croogo::applyHookProperties('Hook.controller_properties');
		parent::__construct();
		if ($this->name == 'CakeError') {
			$this->_set(Router::getPaths());
			$this->params = Router::getParams();
			$this->constructClasses();
			$this->Component->initialize($this);
			$this->beforeFilter();
			$this->Component->triggerCallback('startup', $this);
		}
	}

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		$this->AclFilter->auth();
		$this->RequestHandler->setContent('json', 'text/x-json');
		$this->Security->blackHoleCallback = '__securityError';

		if (isset($this->params['admin']) && $this->name != 'CakeError') {
			$this->layout = 'admin';
		}

		if ($this->RequestHandler->isAjax()) {
			$this->layout = 'ajax';
		}

		if (Configure::read('Site.theme') && !isset($this->params['admin'])) {
			$this->theme = Configure::read('Site.theme');
		} elseif (Configure::read('Site.admin_theme') && isset($this->params['admin'])) {
			$this->theme = Configure::read('Site.admin_theme');
		}

		if (!isset($this->params['admin']) && 
			Configure::read('Site.status') == 0) {
			$this->layout = 'maintenance';
			$this->set('title_for_layout', __('Site down for maintenance', true));
			$this->render('../elements/blank');
		}

		if (isset($this->params['locale'])) {
			Configure::write('Config.language', $this->params['locale']);
		}
	}

/**
 * afterFilter callback
 * Disable debug mode on JSON pages to prevent the script execution time to be appended to the page
 *
 * @see http://croogo.lighthouseapp.com/projects/32818/tickets/216
 * @return void
 */
	public function afterFilter() {
		parent::afterFilter();
		if (!empty($this->params['url']['ext']) && $this->params['url']['ext'] === 'json') {
			Configure::write('debug', 0);
		}
	}

/**
 * blackHoleCallback for SecurityComponent
 *
 * @return void
 */
	public function __securityError() {
		$this->cakeError('securityError');
	}

}
