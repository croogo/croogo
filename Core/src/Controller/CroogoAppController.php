<?php

namespace Croogo\Core\Controller;

use App\Controller\AppController;
use Cake\Controller\ErrorController;
use Cake\Controller\Exception\MissingComponentException;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Utility\Hash;

use Croogo\Core\Croogo;
use Croogo\Core\PropertyHookTrait;
use Croogo\Extensions\CroogoTheme;

/**
 * Croogo App Controller
 *
 * @category Croogo.Controller
 * @package  Croogo.Croogo.Controller
 * @version  1.5
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoAppController extends AppController {

	use PropertyHookTrait;

/**
 * Default Components
 *
 * @var array
 * @access public
 */
	protected $_defaultComponents = array(
		'Croogo/Core.Croogo',
		'Croogo/Acl.Filter',
		'Security',
		'Acl.Acl',
		'Auth' => [
			'authenticate' => [
				'Form' => [
					'passwordHasher' => [
						'className' => 'Fallback',
						'hashers' => ['Default', 'Weak']
					]
				]
			]
		],
		'Flash',
		'RequestHandler',
	);

/**
 * List of registered Application Components
 *g
 * These components are typically hooked into the application during bootstrap.
 * @see Croogo::hookComponent
 */
	protected $_appComponents = array();

/**
 * List of registered API Components
 *
 * These components are typically hooked into the application during bootstrap.
 * @see Croogo::hookApiComponent
 */
	protected $_apiComponents = array();

/**
 * Helpers
 *
 * @var array
 * @access public
 */
	public $helpers = array(
		'Html',
		'Form',
		'Text',
		'Time',
		'Croogo/Core.Layout',
		'Croogo/Core.Custom',
		'Croogo/Core.CroogoForm',
		'Croogo/Core.Theme',
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
//	public $viewClass = 'Theme';

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
 * @param Request $request
 * @param Response $response
 * @param null $name
 */
	public function __construct(Request $request = null, Response $response = null, $name = null) {
		parent::__construct($request, $response, $name);
		if ($request) {
			$request->addDetector('api', array(
				'callback' => array('CroogoRouter', 'isApiRequest'),
			));
			$request->addDetector('whitelisted', array(
				'callback' => array('CroogoRouter', 'isWhitelistedRequest'),
			));
		}
		$this->eventManager()->dispatch(new Event('Controller.afterConstruct', $this));
		$this->afterConstruct();
	}

/**
 * Initialize
 */
	public function initialize() {
		parent::initialize();

		Croogo::applyHookProperties('Hook.controller_properties', $this);
		$this->_setupComponents();
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
		if (empty($this->viewClass)) {
			$this->viewClass = 'Croogo/Core.Croogo';
		}
		Croogo::applyHookProperties('Hook.controller_properties', $this);
		$this->_setupComponents();
	}

/**
 * Setup the components array
 *
 * @param void
 * @return void
 */
	protected function _setupComponents() {
		$components = [];

		if ($this->request && !$this->request->is('api')) {
			$components = Hash::merge(
				$this->_defaultComponents,
				$this->_appComponents
			);
		} else {
			$components = Hash::merge(
				[
					'Acl.Acl',
					'Auth',
					'Security',
					'Flash',
					'RequestHandler',
					'Croogo/Acl.AclFilter'
				],
				$this->_apiComponents
			);

			$apiComponents = array();
			$priority = 8;
			foreach ($this->_apiComponents as $component => $setting) {
				if (is_string($setting)) {
					$component = $setting;
					$setting = array();
				}
				$className = $component;
				list(, $apiComponent) = pluginSplit($component);
				$setting = Hash::merge(compact('className', 'priority'), $setting);
				$apiComponents[$apiComponent] = $setting;
			}
			$this->_apiComponents = $apiComponents;
		}

		foreach ($components as $component => $config) {
			if (!is_array($config)) {
				$component = $config;
				$config = [];
			}

			$this->loadComponent($component, $config);
		}
	}

	public function loadComponent($name, array $config = []) {
		list(, $prop) = pluginSplit($name);
		list(, $modelProp) = pluginSplit($this->modelClass);
		$component = $this->components()->load($name, $config);
		if ($prop !== $modelProp) {
			$this->{$prop} = $component;
		}
		return $component;
	}

/**
 * Setup themes
 *
 * @return void
 */
	protected function _setupTheme() {
		$prefix = isset($this->request->params['prefix']) ? $this->request->params['prefix'] : '';
		if ($prefix === 'admin') {
			$theme = Configure::read('Site.admin_theme');
			if ($theme) {
				App::build(array(
					'View/Helper' => array(App::themePath($theme) . 'Helper' . DS),
				));
			}
			$this->layout = 'Croogo/Core.admin';
		} else {
			$theme = Configure::read('Site.theme');
		}
		$this->theme = $theme;

		$croogoTheme = new CroogoTheme();
		$data = $croogoTheme->getData($theme);
		$settings = $data['settings'];

		if (empty($settings['prefixes']['admin']['helpers']['Croogo/Core.Croogo'])) {
			$this->helpers[] = 'Croogo/Core.Croogo';
		}

		if (isset($settings['prefixes'][$prefix])) {
			foreach ($settings['prefixes'][$prefix]['helpers'] as $helper => $settings) {
				$this->helpers[$helper] = $settings;
			}
		}
	}

/**
 * Allows extending action from component
 *
 * @throws MissingActionException
 */
	public function invokeAction() {
		$request = $this->request;
		try {
			return parent::invokeAction($request);
		} catch (MissingActionException $e) {
			$params = $request->params;
			$prefix = isset($params['prefix']) ? $params['prefix'] : '';
			$action = str_replace($prefix . '_', '', $params['action']);
			foreach ($this->_apiComponents as $component => $setting) {
				if (empty($this->{$component})) {
					continue;
				}
				if ($this->{$component}->isValidAction($action)) {
					$this->setRequest($request);
					return $this->{$component}->{$action}($this);
				}
			}
			throw $e;
		}
	}

/**
 * beforeFilter
 *
 * @return void
 * @throws MissingComponentException
 */
	public function beforeFilter(Event $event) {
		parent::beforeFilter($event);
		$aclFilterComponent = 'Filter';
		if (empty($this->{$aclFilterComponent})) {
			throw new MissingComponentException(array('class' => $aclFilterComponent));
		}
		$this->{$aclFilterComponent}->auth();

		if (!$this->request->is('api')) {
			$this->Security->blackHoleCallback = 'securityError';
			if ($this->request->param('action') == 'delete' && $this->request->param('prefix') == 'admin') {
				$this->request->allowMethod('post');
			}
		}

		$this->_setupTheme();

		if ($this->request->is('ajax')) {
			$this->layout = 'ajax';
		}

		if (
			$this->request->param('prefix') !== 'admin' &&
			Configure::read('Site.status') == 0 &&
			$this->Auth->user('role_id') != 1
		) {
			if (!$this->request->is('whitelisted')) {
				$this->layout = 'Croogo/Core.maintenance';
				$this->response->statusCode(503);
				$this->set('title_for_layout', __d('croogo', 'Site down for maintenance'));
				$this->viewPath = 'Maintenance';
				$this->render('Croogo/Core.blank');
			}
		}

		if (isset($this->request->params['locale'])) {
			Configure::write('Config.language', $this->request->params['locale']);
		}

		if (isset($this->request->params['admin'])) {
			Croogo::dispatchEvent('Croogo.beforeSetupAdminData', $this);
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
//		list($plugin, ) = pluginSplit(App::location(get_parent_class($this)));
//		if ($plugin) {
//			App::build(array(
//				'View' => array(
//					Plugin::path($plugin) . 'View' . DS,
//				),
//			), App::APPEND);
//		}

		if (strpos($view, '/') !== false || $this instanceof ErrorController) {
			return parent::render($view, $layout);
		}

		$fallbackView = $this->__getDefaultFallbackView();
		if (is_null($view) && in_array($this->request->action, array('edit', 'add'))) {
			$viewPaths = App::path('View', $this->plugin);
			$themePath = $this->theme ? App::themePath($this->theme) : null;
			$searchPaths = array_merge((array)$themePath, $viewPaths);
			$view = $this->__findRequestedView($searchPaths);
			if (empty($view)) {
				$view = $fallbackView;
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
	public function beforeRender(Event $event) {
		if (!$this->usePaginationCache) {
			return;
		}
		if (!isset($this->helpers['Paginator']) && !in_array('Paginator', $this->helpers)) {
			$this->helpers[] = 'Paginator';
		}
	}

/**
 * Get Default Fallback View
 *
 * @return string
 */
	private function __getDefaultFallbackView() {
		$fallbackView = 'form';
		if (!empty($this->request->params['prefix']) && $this->request->params['prefix'] === 'admin') {
			$fallbackView = 'form';
		}
		return $fallbackView;
	}

/**
 * Search for existing view override in registered view paths
 *
 * @return string
 */
	private function __findRequestedView($viewPaths) {
		if (empty($viewPaths)) {
			return false;
		}
		foreach ($viewPaths as $path) {
			$file = $this->viewPath . DS . $this->request->action . '.ctp';
			$requested = $path . $file;
			if (file_exists($requested)) {
				return $requested;
			} else {
				if (!$this->plugin) {
					continue;
				}
				$requested = $path . 'Plugin' . DS . $this->plugin . DS . $file;
				if (file_exists($requested)) {
					return $requested;
				}
			}
		}
		return false;
	}
}
