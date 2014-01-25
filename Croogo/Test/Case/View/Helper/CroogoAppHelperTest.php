<?php
App::uses('View', 'View');
App::uses('CroogoAppHelper', 'Croogo.View/Helper');
App::uses('Router', 'Routing');
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

	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.taxonomy.type',
		'plugin.taxonomy.vocabulary',
		'plugin.taxonomy.types_vocabulary',
	);

	public function setUp() {
		parent::setUp();
		CakePlugin::load('Translate');
		$this->View = new View(null);
		$this->AppHelper = new CroogoAppHelper($this->View);
		$this->AppHelper->request = new CakeRequest(null, false);
	}

	public function tearDown() {
		parent::tearDown();
		CakePlugin::unload('Translate');
		unset($this->AppHelper->request, $this->AppHelper, $this->View);
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

}
