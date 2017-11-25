<?php

namespace Croogo\Core\Controller;

use Cake\Controller\ErrorController;
use Cake\Controller\Exception\MissingActionException;
use Cake\Controller\Exception\MissingComponentException;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Utility\Hash;

use Cake\View\Exception\MissingTemplateException;
use Croogo\Core\Croogo;
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
class AppController extends \App\Controller\AppController implements HookableComponentInterface
{

    use HookableComponentTrait;

    /**
     * List of registered API Components
     *
     * These components are typically hooked into the application during bootstrap.
     * @see Croogo::hookApiComponent
     */
    protected $_apiComponents = [];

    /**
     * Pagination
     */
    public $paginate = [
        'limit' => 10,
    ];

    /**
     * Cache pagination results
     *
     * @var boolean
     * @access public
     */
    public $usePaginationCache = true;

    /**
     * Constructor
     *
     * @access public
     * @param Request $request
     * @param Response $response
     * @param null $name
     */
    public function __construct(Request $request = null, Response $response = null, $name = null)
    {
        parent::__construct($request, $response, $name);
        if ($request) {
            $request->addDetector('api', [
                'callback' => ['Croogo\\Core\\Router', 'isApiRequest'],
            ]);
            $request->addDetector('whitelisted', [
                'Croogo\\Core\\Router', 'isWhitelistedRequest',
            ]);
        }
    }

    public function initialize()
    {
        $this->_dispatchBeforeInitialize();

        parent::initialize();

        $this->_setupAclComponent();
    }

    /**
     * {@inheritDoc}
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        if (empty($this->viewBuilder()->className()) || $this->viewBuilder()->className() === 'App\View\AjaxView') {
            unset($this->viewClass);
            $this->viewBuilder()->className('Croogo/Core.Croogo');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render($view = null, $layout = null)
    {
        if ($this->request->param('prefix') === 'admin') {
            Croogo::dispatchEvent('Croogo.setupAdminData', $this);
        }

        // Just render normal when we aren't in a edit or add action
        if (!in_array($this->request->param('action'), ['edit', 'add'])) {
            return parent::render($view, $layout);
        }

        try {
            // First try the edit or add view
            return parent::render($view, $layout);
        } catch (MissingTemplateException $e) {
            // Secondly, when the template isn't found, try form view
            return parent::render('form', $layout);
        }
    }

    /**
     * Allows extending action from component
     *
     * @throws MissingActionException
     */
    public function invokeAction()
    {
        $request = $this->request;
        try {
            return parent::invokeAction();
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
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $aclFilterComponent = 'Filter';
        if (empty($this->{$aclFilterComponent})) {
            throw new MissingComponentException(['class' => $aclFilterComponent]);
        }
        $this->{$aclFilterComponent}->auth();

        if (Configure::read('Site.status') == 0 &&
            $this->Auth->user('role_id') != 1
        ) {
            if (!$this->request->is('whitelisted') &&
                !(
                    $this->request->param('prefix') == 'admin' &&
                    $this->request->param('action') === 'login'
                )
            ) {
                $this->viewBuilder()->setLayout('maintenance');
                $this->response->statusCode(503);
                $this->set('title_for_layout', __d('croogo', 'Site down for maintenance'));
                $this->viewBuilder()->templatePath('Maintenance');
                $this->render('Croogo/Core.blank');
            }
        }

        if (!$this->request->is('api')) {
            $this->Security->blackHoleCallback = '_securityError';
            if ($this->request->param('action') == 'delete' && $this->request->param('prefix') == 'admin') {
                $this->request->allowMethod('post');
            }
        }

        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
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
    public function _securityError($type)
    {
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
    protected function _setupAclComponent()
    {
        $config = Configure::read('Access Control');
        if (isset($config['rowLevel']) && $config['rowLevel'] == true) {
            if (strpos($config['models'], str_replace('/', '\/', $this->modelClass)) === false) {
                return;
            }
            if ($this->request->param('controller')) {
                $this->loadComponent('Croogo/Acl.RowLevelAcl');
            }
        }
    }

    protected function _setupPrg()
    {
        $this->loadComponent('Search.Prg', [
            'actions' => ['index']
        ]);
    }

    public function _loadCroogoComponents(array $components)
    {
        foreach ($components as $component => $options) {
            if (is_string($options)) {
                $component = $options;
                $options = [];
            }
            $this->loadComponent('Croogo/Core.' . $component, $options);
        }
    }
}
