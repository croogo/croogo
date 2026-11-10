<?php

namespace Croogo\Core\Controller;

use Cake\Controller\Exception\MissingActionException;
use Cake\Controller\Exception\MissingComponentException;
use Cake\Core\Configure;
use Cake\Datasource\Paging\PaginatedInterface;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\View\Exception\MissingTemplateException;
use Closure;
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
#[\AllowDynamicProperties]
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
    public array $paginate = [
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
    public function __construct(?ServerRequest $request = null, ?Response $response = null, ?string $name = null)
    {
        // Cake 5: Controller::__construct(request, name, ...) - $response wypadl z sygnatury
        parent::__construct($request ?? new ServerRequest(), $name);
        // Cake 4.5+: Controller ustawia $defaultTable z nazwy tylko gdy $modelClass === null.
        // Croogo ustawia $modelClass w kontrolerach, więc $defaultTable zostawałby null
        // (fetchTable()/Crud -> "must provide $alias or set $defaultTable"). Synchronizujemy.
        if (empty($this->defaultTable) && !empty($this->modelClass)) {
            $this->defaultTable = $this->modelClass;
        }
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
    public function initialize(): void
    {
        $this->_dispatchBeforeInitialize();

        parent::initialize();

        // Cake 5: public $helpers na kontrolerze nie jest juz auto-ladowane. Doladowujemy
        // je do ViewBuildera, zeby istniejace deklaracje ($this->helpers) dzialaly (BC).
        if (property_exists($this, 'helpers') && !empty($this->helpers)) {
            foreach ((array)$this->helpers as $name => $config) {
                is_string($name)
                    ? $this->viewBuilder()->addHelper($name, (array)$config)
                    : $this->viewBuilder()->addHelper($config);
            }
        }

        $this->_setupAclComponent();
    }

    /**
     * {@inheritDoc}
     */
    public function beforeRender(\Cake\Event\EventInterface $event): void
    {
        parent::beforeRender($event);

        if (!$this->_setupLegacySerializedView()) {
            if (empty($this->viewBuilder()->getClassName()) || $this->viewBuilder()->getClassName() === 'App\View\AjaxView') {
                unset($this->viewClass);
                $this->viewBuilder()->setClassName('Croogo/Core.Croogo');
            }
        }

        $this->_restoreLegacyPagingParam();
    }

    /**
     * Cake 5: RequestHandlerComponent usunięty. Kontrolery w stylu Cake 3 sygnalizują
     * odpowiedź JSON zmienną widoku `_serialize` — RequestHandler przełączał wtedy widok
     * na JsonView i honorował tę zmienną. Bez niego beforeRender wymuszał widok Croogo
     * (HTML) także na takiej akcji, więc szukała nieistniejącego szablonu i kończyła się
     * MissingTemplateException — renderowaną jako error400 „The requested address ... was
     * not found on this server." (wyjątek niesie kod 0, a renderer bierze error400 dla
     * wszystkiego < 500), przez co wygląda to na 404 routingu. Realny przypadek:
     * /admin/photo_ai/editor/create-directory.json na prod85.
     *
     * Odpowiednik shimu z Api\AppController, ale WARUNKOWY: admin serwuje HTML, więc
     * bezwarunkowy JsonView położyłby każdy zwykły widok. `_serialize` ustawiają wyłącznie
     * akcje JSON-owe, więc obecność tej zmiennej jest wystarczającym sygnałem.
     *
     * @return bool Czy akcja deklaruje odpowiedź serializowaną (i widok jest już ustawiony).
     */
    protected function _setupLegacySerializedView(): bool
    {
        $builder = $this->viewBuilder();
        $serialize = $builder->getVar('_serialize');
        if ($serialize === null) {
            return false;
        }

        if ($builder->getOption('serialize') === null) {
            $builder->setOption('serialize', $serialize);
        }

        $className = $builder->getClassName();
        if (empty($className) || $className === 'App\View\AjaxView') {
            unset($this->viewClass);
            $builder->setClassName('Json');
        }

        return true;
    }

    /**
     * Cake 5 trzyma dane paginacji tylko w PaginatedResultSet (nie w request params jak
     * Cake 3), a klucze zmieniły semantykę. ~34 szablony admina bramkują blok paginacji na
     * request param 'paging' z legacy kluczami — odtwarzamy go z każdej zmiennej widoku
     * implementującej PaginatedInterface.
     *
     * @return void
     */
    protected function _restoreLegacyPagingParam(): void
    {
        $builder = $this->viewBuilder();
        $paging = [];
        // getVars() w Cake 5 zwraca mape nazwa => wartosc (nie liste nazw)
        foreach ($builder->getVars() as $name => $var) {
            $var = $builder->getVar($name);
            if (!$var instanceof PaginatedInterface) {
                continue;
            }
            $params = $var->pagingParams();
            $alias = $var->pagingParam('alias') ?? $name;
            $paging[$alias] = [
                'page' => $var->currentPage(),
                'current' => $var->count(),
                'count' => $var->totalCount(),
                'perPage' => $var->perPage(),
                'limit' => $var->perPage(),
                'pageCount' => $var->pageCount(),
                'prevPage' => $var->hasPrevPage(),
                'nextPage' => $var->hasNextPage(),
            ] + $params;
        }
        if ($paging) {
            $existing = (array)$this->getRequest()->getParam('paging');
            $this->setRequest($this->getRequest()->withParam('paging', $paging + $existing));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render($view = null, $layout = null): \Cake\Http\Response
    {
        if ($this->getRequest()->getParam('prefix') === 'Admin') {
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
    public function invokeAction(Closure $action, array $args): void
    {
        $request = $this->getRequest();
        try {
            parent::invokeAction($action, $args);
        } catch (MissingActionException $e) {
            $prefix = $request->getParam('prefix', '');
            $actionName = str_replace($prefix . '_', '', $request->getParam('action'));
            foreach ($this->_apiComponents as $component => $setting) {
                if (empty($this->{$component})) {
                    continue;
                }
                if ($this->{$component}->isValidAction($actionName)) {
                    $this->setRequest($request);
                    $this->{$component}->{$actionName}($this);

                    return;
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
    public function beforeFilter(\Cake\Event\EventInterface $event)
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
                    $this->getRequest()->getParam('prefix') == 'Admin' &&
                    $this->getRequest()->getParam('action') === 'login'
                )
            ) {
                $this->viewBuilder()->setLayout('maintenance');
                $this->setResponse($this->getResponse()->withStatus(503));
                $this->set('title_for_layout', __d('croogo', 'Site down for maintenance'));
                $this->viewBuilder()->setTemplatePath('Maintenance');
                $this->render('Croogo/Core.blank');
            }
        }

        if (!$this->getRequest()->is('api')) {
            // Cake 5: SecurityComponent::$blackHoleCallback -> FormProtection validationFailureCallback (Closure).
            $this->FormProtection->setConfig('validationFailureCallback', function ($exception) {
                return $this->_securityError('post', $exception);
            });
            if ($this->getRequest()->getParam('action') == 'delete' && $this->getRequest()->getParam('prefix') == 'Admin') {
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
     * validationFailureCallback for FormProtectionComponent
     *
     * @return \Cake\Http\Response|null
     */
    public function _securityError($type = null, $exception = null): ?Response
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
        if ($this->getRequest()->getParam('prefix') == 'Admin') {
            $theme = Configure::read('Site.admin_theme');
        } else {
            $theme = Configure::read('Site.theme');
        }
        $template = $theme . './Error/security';

        // Cake 5: zwrocony Response staje sie wynikiem eventu Controller.startup,
        // ControllerFactory konczy zadanie sam - bez recznej emisji i exit().
        return $this->render($template)->withStatus(400);
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
        // Search 6: PrgComponent scalony w SearchComponent (Search.Search).
        $this->loadComponent('Search.Search', [
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
