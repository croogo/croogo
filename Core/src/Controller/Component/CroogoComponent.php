<?php

namespace Croogo\Core\Controller\Component;

use Cake\Controller\Controller;
use Cake\Core\App;
use Cake\Controller\Component\AuthComponent;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\ORM\Table;

use Croogo\Core\Exception\Exception;
use Croogo\Core\Croogo;
use Croogo\Core\Nav;
use Croogo\Extensions\CroogoPlugin;
use Croogo\Extensions\CroogoTheme;

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
class CroogoComponent extends Component
{

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
    public $blocksData = [
        'menus' => [],
        'vocabularies' => [],
        'nodes' => [],
    ];

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
    public function __get($name)
    {
        switch ($name) {
            case '_CroogoPlugin':
            case '_CroogoTheme':
                if (!isset($this->{$name})) {
                    $class = 'Croogo\\Extensions\\' . substr($name, 1);
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
 * @param object $event instance of controller
 * @return void
 */
    public function startup(Event $event)
    {
        $this->_controller = $event->subject();

        if ($this->_controller->request->param('prefix') == 'admin') {
            if (!isset($this->_controller->request->params['requested'])) {
                $this->_adminData();
            }
        }
    }

/**
 * Set variables for admin layout
 *
 * @return void
 */
    protected function _adminData()
    {
        if (!Configure::read('Croogo.version')) {
            if (Plugin::loaded('Settings')) {
                if ($this->_controller->Setting instanceof Model) {
                    if (file_exists(APP . 'VERSION.txt')) {
                        $file = APP . 'VERSION.txt';
                    } else {
                        $file = dirname(Plugin::path('Croogo')) . DS . 'VERSION.txt';
                    }
                    $version = trim(file_get_contents($file));
                    $this->_controller->Setting->write('Croogo.version', $version);
                }
            }
        }
        $_siteTitle = Configure::read('Site.title');
        $this->_controller->set(compact('_siteTitle'));
        $this->_adminMenus();
    }

/**
 * Setup admin menu
 */
    protected function _adminMenus()
    {
        Nav::add('top-left', 'site', [
            'icon' => false,
            'title' => __d('croogo', 'Visit website'),
            'url' => '/',
            'weight' => 0,
            'htmlAttributes' => [
                'target' => '_blank',
            ],
        ]);

        $user = $this->request->session()->read('Auth.User');
        $gravatarUrl = '<img src="//www.gravatar.com/avatar/' . md5($user['email']) . '?s=23" class="img-rounded"/> ';
        Nav::add('top-right', 'user', [
            'icon' => false,
            'title' => $user['username'],
            'before' => $gravatarUrl,
            'url' => '#',
            'children' => [
                'profile' => [
                    'title' => __d('croogo', 'Profile'),
                    'icon' => 'user',
                    'url' => [
                        'prefix' => 'admin',
                        'plugin' => 'Croogo/Users',
                        'controller' => 'Users',
                        'action' => 'view',
                        $user['id'],
                    ],
                ],
                'separator-1' => [
                    'separator' => true,
                ],
                'logout' => [
                    'icon' => 'power-off',
                    'title' => __d('croogo', 'Logout'),
                    'url' => [
                        'prefix' => 'admin',
                        'plugin' => 'Croogo/Users',
                        'controller' => 'Users',
                        'action' => 'logout',
                    ],
                ],
            ],
        ]);
    }

/**
 * Gets the Role Id of the current user
 *
 * @return integer Role Id
 */
    public function roleId()
    {
        $roleId = $this->_controller->request->session()->read('Auth.User.role_id');
        return $roleId ? $roleId : $this->_defaultRoleId;
    }

/**
 * Extracts parameters from 'filter' named parameter.
 *
 * @return array
 * @deprecated use Search plugin to perform filtering
 */
    public function extractFilter()
    {
        $filter = explode(';', $this->_controller->request->params['named']['filter']);
        $filterData = [];
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
    public function getRelativePath($url = '/')
    {
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
    public function addAco($action, $allowRoles = [])
    {
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
    public function removeAco($action)
    {
        $this->_controller->CroogoAccess->removeAco($action);
    }

/**
 * Sets the referer page
 *
 * We need to know where were you, to get you back there
 *
 * @return void
 * @see CroogoComponent::redirect()
 * @deprecated Use Crud.beforeRedirect event and AppController::redirectToSelf
 */
    public function setReferer()
    {
        $default = [
            'controller' => $this->_controller->request->params['controller'],
            'action' => 'index',
        ];
        $referer = $this->_controller->referer($default, true);
        $this->_controller->request->session()->write('Croogo.referer', ['url' => $referer]);
    }

/**
 * Croogo flavored redirect
 *
 * If 'save' pressed, redirect to referer or $indexUrl instead of 'edit'
 *
 * @param string $url
 * @param int $status
 * @param bool $exit
 * @param array $indexUrl
 * @return void|\Cake\Network\Response
 * @see CroogoComponent::setReferer()
 * @deprecated Use Crud.beforeRedirect event and AppController::redirectToSelf
 */
    public function redirect($url, $status = null, $exit = true, $indexUrl = [])
    {
        $referer = $this->_controller->request->session()->read('Croogo.referer');
        $this->_controller->request->session()->delete('Croogo.referer');
        if (is_array($url)) {
            if (isset($url['action']) && $url['action'] === 'edit') {
                if (!isset($this->_controller->request->data['_apply'])) {
                    $url = !empty($indexUrl) ? $indexUrl : ['action' => 'index'];
                }
            } elseif (isset($referer['url'])) {
                $url = $referer['url'];
            }
        }
        return $this->_controller->redirect($url, $status);
    }

/**
 * Toggle field status
 *
 * @param $table Table instance
 * @param $id integer Model id
 * @param $status integer current status
 * @param $field string field name to toggle
 * @throws Exception
 */
    public function fieldToggle(Table $table, $id, $status, $field = 'status')
    {
        if (empty($id) || $status === null) {
            throw new Exception(__d('croogo', 'Invalid content'));
        }

        $status = (int)!$status;

        $entity = $table->get($id);
        $entity->{$field} = $status;
        $this->_controller->viewBuilder()->setLayout('ajax');
        if ($table->save($entity)) {
            $this->_controller->set(compact('id', 'status'));
            $this->_controller->render('Croogo/Core./Common/admin_toggle');
        } else {
            throw new Exception(__d('croogo', 'Failed toggling field %s to %s', $field, $status));
        }
    }

/**
 * Loads plugin's bootstrap.php file
 *
 * @param string $plugin Plugin name (underscored)
 * @return void
 * @deprecated use CroogoPlugin::addBootstrap()
 */
    public function addPluginBootstrap($plugin)
    {
        $this->_CroogoPlugin->addBootstrap($plugin);
    }

/**
 * Plugin name will be removed from Hook.bootstraps
 *
 * @param string $plugin Plugin name (underscored)
 * @return void
 * @deprecated use CroogoPlugin::removeBootstrap()
 */
    public function removePluginBootstrap($plugin)
    {
        $this->_CroogoPlugin->removeBootstrap($plugin);
    }

/**
 * Get theme aliases (folder names)
 *
 * @return array
 * @deprecated use CroogoTheme::getThemes()
 */
    public function getThemes()
    {
        return $this->_CroogoTheme->getThemes();
    }

/**
 * Get the content of theme.json file from a theme
 *
 * @param string $alias theme folder name
 * @return array
 * @deprecated use CroogoTheme::getData()
 */
    public function getThemeData($alias = null)
    {
        return $this->_CroogoTheme->getData($alias);
    }

/**
 * Get plugin alises (folder names)
 *
 * @return array
 * @deprecated use CroogoPlugin::getPlugins()
 */
    public function getPlugins()
    {
        return $this->_CroogoPlugin->getPlugins();
    }

/**
 * Get the content of plugin.json file of a plugin
 *
 * @param string $alias plugin folder name
 * @return array
 * @deprecated use CroogoPlugin::getData
 */
    public function getPluginData($alias = null)
    {
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
    public function checkPluginDependency($plugin = null)
    {
        return $this->_CroogoPlugin->checkDependency($plugin);
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
    protected function _setupViewPaths(Controller $controller)
    {
        $defaultViewPaths = App::path('Template');
        $pos = array_search(APP . 'Template' . DS, $defaultViewPaths);
        if ($pos !== false) {
            $viewPaths = array_splice($defaultViewPaths, 0, $pos + 1);
        } else {
            $viewPaths = $defaultViewPaths;
        }
        if ($controller->viewBuilder()->theme()) {
            $themePaths = App::path('Template', $controller->viewBuilder()->theme());
            foreach ($themePaths as $themePath) {
                $viewPaths[] = $themePath;
                if ($controller->plugin) {
                    $viewPaths[] = $themePath . 'Plugin' . DS . $controller->plugin . DS;
                }
            }
        }
        if ($controller->plugin) {
            $viewPaths = array_merge($viewPaths, App::path('Template', $controller->plugin));
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
 * @param string|array $templates view path or array of view paths
 * @return void
 */
    public function viewFallback($templates)
    {
        $templates = (array)$templates;
        $controller = $this->_controller;
        $templatePaths = $this->_setupViewPaths($controller);
        foreach ($templates as $template) {
            foreach ($templatePaths as $templatePath) {
                $templatePath = $templatePath . $this->_viewPath() . DS . $template;
                if (file_exists($templatePath . '.ctp')) {
                    $controller->viewBuilder()->template($this->_viewPath() . DS . $template);
                    return;
                }
            }
        }
    }

    protected function _viewPath()
    {
        $viewPath = $this->_controller->name;
        if (!empty($this->request->params['prefix'])) {
            $prefixes = array_map(
                'Cake\Utility\Inflector::camelize',
                explode('/', $this->_controller->request->params['prefix'])
            );
            $viewPath = implode(DS, $prefixes) . DS . $viewPath;
        }
        return $viewPath;
    }

    public function protectToggleAction()
    {
        $controller = $this->getController();
        if ($controller->request->action !== 'toggle') {
            return;
        }
        if (!$controller->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $controller->eventManager()->off($controller->Csrf);
        $controller->Security->config('validatePost', false);
    }
}
