<?php

App::uses('Controller', 'Controller');

/**
 * Croogo App Controller
 *
 * PHP version 5
 *
 * @category Croogo.Controller
 * @package  Croogo.Croogo.Controller
 * @version  1.5
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoAppController extends Controller {

/**
 * Components
 *
 * @var array
 * @access public
 */
	public $components = array(
		'Croogo.Croogo',
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
		'Croogo.Layout',
		'Custom',
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
		parent::__construct($request, $response);
		$this->getEventManager()->dispatch(new CakeEvent('Controller.afterConstruct', $this));
	}

/**
 * implementedEvents
 */
	public function implementedEvents() {
		return parent::implementedEvents() + array(
			'Controller.afterConstruct' => 'afterConstruct',
		);
	}

/**
 * afterConstruct
 *
 * called when Controller::__construct() is complete.
 * Override this method to perform class configuration/initialization that
 * needs to be performed earlier from Controller::beforeFilter().
 *
 * You still need to call parent::afterConstruct() method to ensure correct
 * behavior.
 */
	public function afterConstruct() {
		Croogo::applyHookProperties('Hook.controller_properties', $this);
		if (isset($this->request->params['admin'])) {
			$this->helpers[] = 'Croogo.Croogo';
			if (empty($this->helpers['Html'])) {
				$this->helpers['Html'] = array('className' => 'Croogo.CroogoHtml');
			}
			if (empty($this->helpers['Form'])) {
				$this->helpers['Form'] = array('className' => 'Croogo.CroogoForm');
			}
			if (empty($this->helpers['Paginator'])) {
				$this->helpers['Paginator'] = array('className' => 'Croogo.CroogoPaginator');
			}
		}
	}

/**
 * beforeFilter
 *
 * @return void
 * @throws MissingComponentException
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$aclFilterComponent = Configure::read('Site.acl_plugin') . 'Filter';
		if (empty($this->{$aclFilterComponent})) {
			throw new MissingComponentException(array('class' => $aclFilterComponent));
		}
		$this->{$aclFilterComponent}->auth();
		$this->RequestHandler->setContent('json', array('text/x-json', 'application/json'));
		$this->Security->blackHoleCallback = 'securityError';
		$this->Security->requirePost('admin_delete');

		if (isset($this->request->params['admin'])) {
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
			$this->response->statusCode(503);
			$this->set('title_for_layout', __d('croogo', 'Site down for maintenance'));
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
	public function securityError($type) {
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
		$this->response = $this->render('../Errors/security');
		$this->response->statusCode(400);
		$this->response->send();
		$this->_stop();
		return false;
	}

/**
 * _setupAclComponent
 */
	protected function _setupAclComponent() {
		$config = Configure::read('Access Control');
		if (isset($config['rowLevel']) && $config['rowLevel'] == true) {
			if (strpos($config['models'], $this->plugin . '.' . $this->modelClass) === false) {
				return;
			}
			$this->Components->load(Configure::read('Site.acl_plugin') . '.RowLevelAcl');
		}
	}

/**
 * Combine add and edit views
 *
 * @see Controller::render()
 */
	public function render($view = null, $layout = null) {
		if (strpos($view, '/') !== false) {
			return parent::render($view, $layout);
		}
		$viewPaths = App::path('View', $this->plugin);
		$rootPath = $viewPaths[0] . $this->viewPath . DS;
		$requested = $rootPath . $view . '.ctp';
		if (in_array($this->request->action, array('admin_edit', 'admin_add', 'edit', 'add'))) {
			$viewPath = $rootPath . $this->request->action . '.ctp';
			if (!file_exists($requested) && !file_exists($viewPath)) {
				if (strpos($this->request->action, 'admin_') === false) {
					$view = 'form';
				} else {
					$view = 'admin_form';
				}
			}
		}
		return parent::render($view, $layout);
	}

/**
 * Croogo uses this callback to load Paginator helper when one is not supplied.
 * This is required so that pagination variables are correctly set with caching
 * is used.
 *
 * @return void
 * @see Controller::beforeRender()
 */
	public function beforeRender() {
		if (!$this->usePaginationCache) {
			return;
		}
		if (!isset($this->helpers['Paginator']) && !in_array('Paginator', $this->helpers)) {
			$this->helpers[] = 'Paginator';
		}
	}

}
