<?php

namespace Croogo\Core\Controller;

use Cake\Controller\Exception\MissingActionException;
use Cake\Controller\Exception\MissingComponentException;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Http\ResponseEmitter;
use Cake\Http\ServerRequest;
use Cake\View\Exception\MissingTemplateException;
use Croogo\Core\Croogo;

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
    public function __construct(ServerRequest $request = null, Response $response = null, $name = null)
    {
        parent::__construct($request, $response, $name);
        if ($request) {
            $request->addDetector('api', [
                'callback' => ['Croogo\\Core\\Router', 'isApiRequest'],
            ]);
            $request->addDetector('whitelisted', [
                'Croogo\\Core\\Router',
                'isWhitelistedRequest',
            ]);
        }
    }

    /**
     * @return void
     */
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

        if (empty($this->viewBuilder()->getClassName()) || $this->viewBuilder()->getClassName() === 'App\View\AjaxView') {
            unset($this->viewClass);
            $this->viewBuilder()->setClassName('Croogo/Core.Croogo');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render($view = null, $layout = null)
    {
        if ($this->getRequest()->getParam('prefix') === 'admin') {
            Croogo::dispatchEvent('Croogo.setupAdminData', $this);
        }

        // Just render normal when we aren't in a edit or add action
        if (!in_array($this->getRequest()->getParam('action'), ['edit', 'add'])) {
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
            if (!$this->getRequest()->is('whitelisted') &&
                !(
                    $this->getRequest()->getParam('prefix') == 'admin' &&
                    $this->getRequest()->getParam('action') === 'login'
                )
            ) {
                $this->viewBuilder()->setLayout('maintenance');
                $this->response->statusCode(503);
                $this->set('title_for_layout', __d('croogo', 'Site down for maintenance'));
                $this->viewBuilder()->templatePath('Maintenance');
                $this->render('Croogo/Core.blank');
            }
        }

        if (!$this->getRequest()->is('api')) {
            $this->Security->blackHoleCallback = '_securityError';
            if ($this->getRequest()->getParam('action') == 'delete' && $this->getRequest()->getParam('prefix') == 'admin') {
                $this->getRequest()->allowMethod('post');
            }
        }

        if ($this->getRequest()->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }

        if ($this->getRequest()->getParam('locale')) {
            Configure::write('Config.language', $this->getRequest()->getParam('locale'));
        }
    }

    /**
     * blackHoleCallback for SecurityComponent
     *
     * @return bool
     */
    public function _securityError($type = null, $exception = null)
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
        $message = $exception ? $exception->getMessage() : null;
        $this->set(compact('type', 'message'));
        if ($this->getRequest()->getParam('prefix') == 'admin') {
            $theme = Configure::read('Site.admin_theme');
        } else {
            $theme = Configure::read('Site.theme');
        }
        $template = $theme . './Error/security';
        $response = $this->render($template);
        $response = $response->withStatus(400);
        $emitter = new ResponseEmitter();
        $emitter->emit($this->response);
        exit(-1);
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
            if ($this->getRequest()->getParam('controller')) {
                $this->loadComponent('Croogo/Acl.RowLevelAcl');
            }
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function _setupPrg()
    {
        $this->loadComponent('Search.Prg', [
            'queryStringWhitelist' => ['sort', 'direction', 'limit', 'chooser'],
            'actions' => ['index']
        ]);
    }

    /**
     * @param array $components
     * @return void
     * @throws \Exception
     */
    protected function _loadCroogoComponents(array $components)
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
