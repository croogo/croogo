<?php

/**
 * Error Handling Controller
 *
 * Controller used by ErrorHandler to render error views.  This is based
 * on CakePHP's own CakeErrorController with the following differences:
 * - inherits from Controller, instead of AppController
 * - loads its own set of components and helpers
 * - aware of Site.theme and Site.admin_theme
 */
class CakeErrorController extends AppController {

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
			CakeLog::write('critical', __('Errors in CakeErrorController: %s', $e->getMessage()));
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
