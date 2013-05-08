<?php

App::uses('AppController', 'Controller');

/**
 * Error Handling Controller
 *
 * Controller used by ErrorHandler to render error views.  This is based
 * on CakePHP's own CakeErrorController with the following differences:
 * - loads its own set of components and helpers
 * - aware of Site.theme and Site.admin_theme
 *
 * PHP version 5
 *
 * @category Controllers
 * @package  Croogo.Croogo.Controller
 * @version  1.0
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoErrorController extends AppController {

/**
 * Models
 *
 * @var array
 * @access public
 */
	public $uses = array();

/**
 * View
 *
 * @var string
 * @access public
 */
	public $viewClass = 'Theme';

/**
 * __construct
 *
 * @param CakeRequest $request
 * @param CakeResponse $response
 */
	public function __construct(CakeRequest $request, CakeResponse $response) {
		parent::__construct($request, $response);
		if (count(Router::extensions())) {
			$this->components[] = 'RequestHandler';
		}

		try {
			$this->constructClasses();
			$this->startupProcess();
		}
		catch (CakeException $e) {
			CakeLog::write('critical', __d('croogo', 'Errors in CakeErrorController: %s', $e->getMessage()));
		}

		$this->_set(array('cacheAction' => false, 'viewPath' => 'Errors'));
		if (isset($this->RequestHandler)) {
			$this->RequestHandler->startup($this);
		}
	}

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		if (Configure::read('Site.theme') && !isset($this->request->params['admin'])) {
			$this->theme = Configure::read('Site.theme');
		} elseif (isset($this->request->params['admin'])) {
			if ($adminTheme = Configure::read('Site.admin_theme')) {
				$this->theme = $adminTheme;
			}
			$this->layout = 'admin_full';
		}
	}

/**
 * Escapes the viewVars.
 *
 * @return void
 */
	public function beforeRender() {
		parent::beforeRender();
		foreach ($this->viewVars as $key => $value) {
			if (!is_object($value)) {
				$this->viewVars[$key] = h($value);
			}
		}
	}

}
