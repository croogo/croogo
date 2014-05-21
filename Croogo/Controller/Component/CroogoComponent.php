<?php

namespace Croogo\Croogo\Controller\Component;
App::uses('AuthComponent', 'Controller/Component');
App::uses('Component', 'Controller');
App::uses('CroogoPlugin', 'Extensions.Lib');
App::uses('CroogoTheme', 'Extensions.Lib');
App::uses('Croogo', 'Croogo.Lib');

/**
 * Croogo Component
 *
 * @category Component
 * @package  Croogo.Croogo.Controller.Component
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoComponent extends Component {

/**
 * Other components used by this component
 *
 * @var array
 * @access public
 */
	public $components = array(
		'Session',
	);

/**
 * Default Role ID
 *
 * Default is 3 (public)
 *
 * @var integer
 */
	protected $_defaultRoleId = 3;

/**
 * Blocks data: contains parsed value of bb-code like strings
 *
 * @var array
 * @access public
 */
	public $blocksData = array(
		'menus' => array(),
		'vocabularies' => array(),
		'nodes' => array(),
	);

/**
 * controller
 *
 * @var Controller
 */
	protected $_controller = null;

/**
 * Method to lazy load classes
 *
 * @return Object
 */
	public function __get($name) {
		switch ($name) {
			case '_CroogoPlugin':
			case '_CroogoTheme':
				if (!isset($this->{$name})) {
					$class = substr($name, 1);
					$this->{$name} = new $class();
					if (method_exists($this->{$name}, 'setController')) {
						$this->{$name}->setController($this->_controller);
					}
				}
				return $this->{$name};
			case 'roleId':
				return $this->roleId();
			default:
				return parent::__get($name);
		}
	}

/**
 * Startup
 *
 * @param object $controller instance of controller
 * @return void
 */
	public function startup(Controller $controller) {
		$this->_controller = $controller;

		if (isset($this->_controller->request->params['admin'])) {
			if (!isset($this->_controller->request->params['requested'])) {
				$this->_adminData();
			}
			$this->_adminMenus();
		}
	}

/**
 * Set variables for admin layout
 *
 * @return void
 */
	protected function _adminData() {
		if (!Configure::read('Croogo.version')) {
			if (CakePlugin::loaded('Settings')) {
				if ($this->_controller->Setting instanceof Model) {
					if (file_exists(APP . 'VERSION.txt')) {
						$file = APP . 'VERSION.txt';
					} else {
						$file = dirname(CakePlugin::path('Croogo')) . DS . 'VERSION.txt';
					}
					$version = trim(file_get_contents($file));
					$this->_controller->Setting->write('Croogo.version', $version);
				}
			}
		}
		$this->_adminMenus();
	}

/**
 * Setup admin menu
 */
	protected function _adminMenus() {
		CroogoNav::add('top-left', 'site', array(
			'icon' => false,
			'title' => __d('croogo', 'Visit website'),
			'url' => '/',
			'weight' => 0,
			'htmlAttributes' => array(
				'target' => '_blank',
			),
		));

		$user = $this->Session->read('Auth.User');
		$gravatarUrl = '<img src="http://www.gravatar.com/avatar/' . md5($user['email']) . '?s=23" class="img-rounded"/> ';
		CroogoNav::add('top-right', 'user', array(
			'icon' => false,
			'title' => $user['username'],
			'before' => $gravatarUrl,
			'url' => '#',
			'children' => array(
				'profile' => array(
					'title' => __d('croogo', 'Profile'),
					'icon' => 'user',
					'url' => array(
						'admin' => true,
						'plugin' => 'users',
						'controller' => 'users',
						'action' => 'edit',
						$user['id'],
					),
				),
				'separator-1' => array(
					'separator' => true,
				),
				'logout' => array(
					'icon' => 'off',
					'title' => 'Logout',
					'url' => array(
						'admin' => true,
						'plugin' => 'users',
						'controller' => 'users',
						'action' => 'logout',
					),
				),
			),
		));
	}

/**
 * Gets the Role Id of the current user
 *
 * @return integer Role Id
 */
	public function roleId() {
		$roleId = AuthComponent::user('role_id');
		return $roleId ? $roleId : $this->_defaultRoleId;
	}

/**
 * Extracts parameters from 'filter' named parameter.
 *
 * @return array
 * @deprecated use Search plugin to perform filtering
 */
	public function extractFilter() {
		$filter = explode(';', $this->_controller->request->params['named']['filter']);
		$filterData = array();
		foreach ($filter as $f) {
			$fData = explode(':', $f);
			$fKey = $fData['0'];
			if ($fKey != null) {
				$filterData[$fKey] = $fData['1'];
			}
		}
		return $filterData;
	}

/**
 * Get URL relative to the app
 *
 * @param array $url
 * @return array
 * @deprecated Use Croogo::getRelativePath
 */
	public function getRelativePath($url = '/') {
		return Croogo::getRelativePath($url);
	}

/**
 * ACL: add ACO
 *
 * Creates ACOs with permissions for roles.
 *
 * @param string $action possible values: ControllerName, ControllerName/method_name
 * @param array $allowRoles Role aliases
 * @return void
 */
	public function addAco($action, $allowRoles = array()) {
		$this->_controller->CroogoAccess->addAco($action, $allowRoles);
	}

/**
 * ACL: remove ACO
 *
 * Removes ACOs and their Permissions
 *
 * @param string $action possible values: ControllerName, ControllerName/method_name
 * @return void
 */
	public function removeAco($action) {
		$this->_controller->CroogoAccess->removeAco($action);
	}

/**
 * Sets the referer page
 *
 * We need to know where were you, to get you back there
 *
 * @return void
 * @see CroogoComponent::redirect()
 */
	public function setReferer() {
		$default = array(
			'controller' => $this->_controller->request->params['controller'],
			'action' => 'index',
		);
		$referer = $this->_controller->referer($default, true);
		$this->Session->write('Croogo.referer', array('url' => $referer));
	}

/**
 * Croogo flavored redirect
 *
 * If 'save' pressed, redirect to referer or 'index' action instead of 'edit'
 *
 * @param string $url
 * @param integer $status
 * @param boolean $exit
 * @return void
 * @see CroogoComponent::setReferer()
 */
	public function redirect($url, $status = null, $exit = true) {
		$referer = $this->Session->read('Croogo.referer');
		$this->Session->delete('Croogo.referer');
		if (is_array($url)) {
			if (isset($url['action']) && $url['action'] === 'edit') {
				if (!isset($this->_controller->request->data['apply'])) {
					$url = array('action' => 'index');
				}
			} elseif (isset($referer['url'])) {
				$url = $referer['url'];
			}
		}
		$this->_controller->redirect($url, $status, $exit);
	}

/**
 * Toggle field status
 *
 * @param $model Model instance
 * @param $id integer Model id
 * @param $status integer current status
 * @param $field string field name to toggle
 * @throws CakeException
 */
	public function fieldToggle(Model $model, $id, $status, $field = 'status') {
		if (empty($id) || $status === null) {
			throw new CakeException(__d('croogo', 'Invalid content'));
		}
		$model->id = $id;
		$status = (int)!$status;
		$this->_controller->layout = 'ajax';
		if ($model->saveField($field, $status)) {
			$this->_controller->set(compact('id', 'status'));
			$this->_controller->render('Common/admin_toggle');
		} else {
			throw new CakeException(__d('croogo', 'Failed toggling field %s to %s', $field, $status));
		}
	}

/**
 * Loads plugin's bootstrap.php file
 *
 * @param string $plugin Plugin name (underscored)
 * @return void
 * @deprecated use CroogoPlugin::addBootstrap()
 */
	public function addPluginBootstrap($plugin) {
		$this->_CroogoPlugin->addBootstrap($plugin);
	}

/**
 * Plugin name will be removed from Hook.bootstraps
 *
 * @param string $plugin Plugin name (underscored)
 * @return void
 * @deprecated use CroogoPlugin::removeBootstrap()
 */
	public function removePluginBootstrap($plugin) {
		$this->_CroogoPlugin->removeBootstrap($plugin);
	}

/**
 * Get theme aliases (folder names)
 *
 * @return array
 * @deprecated use CroogoTheme::getThemes()
 */
	public function getThemes() {
		return $this->_CroogoTheme->getThemes();
	}

/**
 * Get the content of theme.json file from a theme
 *
 * @param string $alias theme folder name
 * @return array
 * @deprecated use CroogoTheme::getData()
 */
	public function getThemeData($alias = null) {
		return $this->_CroogoTheme->getData($alias);
	}

/**
 * Get plugin alises (folder names)
 *
 * @return array
 * @deprecated use CroogoPlugin::getPlugins()
 */
	public function getPlugins() {
		return $this->_CroogoPlugin->getPlugins();
	}

/**
 * Get the content of plugin.json file of a plugin
 *
 * @param string $alias plugin folder name
 * @return array
 * @deprecated use CroogoPlugin::getData
 */
	public function getPluginData($alias = null) {
		return $this->_CroogoPlugin->getData($alias);
	}

/**
 * Check if plugin is dependent on any other plugin.
 * If yes, check if that plugin is available in plugins directory.
 *
 * @param  string $plugin plugin alias (underscrored)
 * @return boolean
 * @deprecated use CroogoPlugin::checkDependency()
 */
	public function checkPluginDependency($plugin = null) {
		return $this->_CroogoPlugin->checkDependency($plugin);
	}

/**
 * Check if plugin is active
 *
 * @param  string $plugin Plugin name (underscored)
 * @return boolean
 * @deprecated use CroogoPlugin::isActive
 */
	public function pluginIsActive($plugin) {
		return $this->_CroogoPlugin->isActive($plugin);
	}

/**
 * Get a list of possible view paths for current request
 *
 * The default view paths are retrieved view App::path('View').  This method
 * injects the theme path and also considers whether a plugin is used.
 *
 * The paths that will be used for fallback is typically:
 *
 *   - APP/View/<Controller>
 *   - APP/Themed/<Theme>/<Controller>
 *   - APP/Themed/<Theme>/Plugin/<Plugin>/<Controller>
 *   - APP/Plugin/<Plugin/View/<Controller>
 *   - APP/Vendor/croogo/croogo/Croogo/View
 *
 * @param Controller $controller
 * @return array A list of view paths
 */
	protected function _setupViewPaths(Controller $controller) {
		$defaultViewPaths = App::path('View');
		$pos = array_search(APP . 'View' . DS, $defaultViewPaths);
		if ($pos !== false) {
			$viewPaths = array_splice($defaultViewPaths, 0, $pos + 1);
		} else {
			$viewPaths = $defaultViewPaths;
		}
		if ($controller->theme) {
			$themePath = App::themePath($controller->theme);
			$viewPaths[] = $themePath;
			if ($controller->plugin) {
				$viewPaths[] = $themePath . 'Plugin' . DS . $controller->plugin . DS;
			}
		}
		if ($controller->plugin) {
			$viewPaths = array_merge($viewPaths, App::path('View', $controller->plugin));
		}
		$viewPaths = array_merge($viewPaths, $defaultViewPaths);
		return $viewPaths;
	}

/**
 * View Fallback
 *
 * Looks for view file through the available view paths.  If the view is found,
 * set Controller::$view variable.
 *
 * @param string|array $views view path or array of view paths
 * @return void
 */
	public function viewFallback($views) {
		if (is_string($views)) {
			$views = array($views);
		}
		$controller = $this->_controller;
		$viewPaths = $this->_setupViewPaths($controller);
		foreach ($views as $view) {
			foreach ($viewPaths as $viewPath) {
				$viewPath = $viewPath . $controller->name . DS . $view . $controller->ext;
				if (file_exists($viewPath)) {
					$controller->view = $viewPath;
					return;
				}
			}
		}
	}

}
