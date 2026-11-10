<?php

namespace Croogo\Core\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Croogo\Core\Exception\Exception;
use Croogo\Core\Nav;

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
     * Startup
     *
     * @param object $event instance of controller
     * @return void
     */
    public function startup(\Cake\Event\EventInterface $event): void
    {
        $this->_controller = $event->getSubject();

        if ($this->_controller->getRequest()->getParam('prefix') == 'Admin') {
            if (!$this->_controller->getRequest()->getParam('requested')) {
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
        $_siteTitle = Configure::read('Site.title');
        $this->_controller->set(compact('_siteTitle'));
        $this->_adminMenus();
    }

    /**
     * Setup admin menu
     */
    protected function _adminMenus()
    {
        $user = $this->getController()->getRequest()->getSession()->read('Auth.User');
        if (empty($user)) {
            return;
        }
    }

    /**
     * Gets the Role Id of the current user
     *
     * @return int Role Id
     */
    public function roleId()
    {
        $roleId = $this->_controller->getRequest()->getSession()->read('Auth.User.role_id');
        if ($roleId) {
            return $roleId;
        }

        return \Cake\ORM\TableRegistry::getTableLocator()->get('Croogo/Users.Roles')
            ->getBehavior('Aliasable')->byAlias('public');
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
    public function addAco($action, $allowRoles = []): void
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
    public function removeAco($action): void
    {
        $this->_controller->CroogoAccess->removeAco($action);
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
        if ($controller->viewBuilder()->getTheme()) {
            $themePaths = App::path('Template', $controller->viewBuilder()->getTheme());
            foreach ($themePaths as $themePath) {
                $viewPaths[] = $themePath;
                if ($controller->getPlugin()) {
                    $viewPaths[] = $themePath . 'Plugin' . DS . $controller->getPlugin() . DS;
                }
            }
        }
        if ($controller->getPlugin()) {
            $viewPaths = array_merge($viewPaths, App::path('Template', $controller->getPlugin()));
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
    public function viewFallback($templates): void
    {
        $templates = (array)$templates;
        $controller = $this->_controller;
        $templatePaths = $this->_setupViewPaths($controller);
        foreach ($templates as $template) {
            foreach ($templatePaths as $templatePath) {
                $templatePath = $templatePath . $this->_viewPath() . DS . $template;
                if (file_exists($templatePath . '.ctp')) {
                    $controller->viewBuilder()->setTemplate($this->_viewPath() . DS . $template);

                    return;
                }
            }
        }
    }

    protected function _viewPath()
    {
        $viewPath = $this->_controller->getName();
        if (!empty($this->getController()->getRequest()->getParam('prefix'))) {
            $prefixes = array_map(
                'Cake\Utility\Inflector::camelize',
                explode('/', $this->_controller->getRequest()->getParam('prefix'))
            );
            $viewPath = implode(DS, $prefixes) . DS . $viewPath;
        }

        return $viewPath;
    }

    public function protectToggleAction()
    {
        $controller = $this->getController();
        if ($controller->getRequest()->getParam('action') !== 'toggle') {
            return;
        }
        if (!$controller->getRequest()->is('post')) {
            throw new \Cake\Http\Exception\MethodNotAllowedException();
        }
        $controller->FormProtection->setConfig('validate', false);
    }
}
