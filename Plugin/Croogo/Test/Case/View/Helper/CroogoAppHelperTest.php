<?php
App::uses('View', 'View');
App::uses('CroogoAppHelper', 'Croogo.View/Helper');
App::uses('Router', 'Routing');
App::uses('CroogoRouter', 'Croogo.Lib');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class CroogoAppHelperTest extends CroogoTestCase {

/**
 * View instance
 *
 * @var View
 */
	public $View;

/**
 * AppHelper instance
 *
 * @var AppHelper
 */
	public $AppHelper;

	public function setUp() {
		parent::setUp();
		Router::$initialized = true;
		CakePlugin::load('Translate', array('bootstrap' => true));
		CroogoRouter::localize();
		$this->View = new View(null);
		$this->AppHelper = new CroogoAppHelper($this->View);
		$this->AppHelper->request = new CakeRequest(null, false);
	}

	public function testUrlWithoutLocale() {
		$url = $this->AppHelper->url();
		$this->assertEqual($url, Router::url('/'));
	}

	public function testUrlWithLocale() {
		$url = $this->AppHelper->url(array('locale' => 'por'));
		$this->assertEqual($url, Router::url('/por/index'));
	}

	public function testFullUrlWithLocale() {
		$url = $this->AppHelper->url(array('locale' => 'por'), true);
		$this->assertEqual($url, Router::url('/por/index', true));
	}

	public function testUrlWithRequestParams() {
		$this->AppHelper->request->params['locale'] = 'por';
		$url = $this->AppHelper->url();
		$this->assertEqual($url, Router::url('/por/index'));
	}

	public function testFullUrlWithRequestParams() {
		$this->AppHelper->request->params['locale'] = 'por';
		$url = $this->AppHelper->url(null, true);
		$this->assertEqual($url, Router::url('/por/index', true));
	}

	public function testUrlWithStringParameterContainingLocale() {
		$this->AppHelper->request->params['locale'] = 'fra';
		$url = $this->AppHelper->url('/fra/index', true);
		$expected = Router::url('/fra/index', true);

		$this->assertEquals($url, $expected);
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->AppHelper->request, $this->AppHelper, $this->View);
	}

}
