<?php
namespace Croogo\Core\Test\TestCase\View\Helper;

use Cake\Core\Plugin;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Routing\Router;
use Cake\View\View;
use Croogo\Core\TestSuite\CroogoTestCase;
use Croogo\Core\View\Helper\CroogoAppHelper;

class CroogoAppHelperTest extends CroogoTestCase
{

/**
 * View instance
 *
 * @var View
 */
    public $View;

/**
 * AppHelper instance
 *
 * @var CroogoAppHelper
 */
    public $AppHelper;

    public $fixtures = [
//		'plugin.croogo\settings.setting',
//		'plugin.taxonomy.type',
//		'plugin.taxonomy.vocabulary',
//		'plugin.taxonomy.types_vocabulary',
    ];

    public function setUp()
    {
        parent::setUp();

        Plugin::load('Croogo/Translate', ['autoload' => true, 'path' => '../Translate/']);

        $request = new Request();
        $this->View = new View($request, new Response());
        $this->AppHelper = new CroogoAppHelper($this->View);
        $this->AppHelper->request = $request;
    }

    public function tearDown()
    {
        parent::tearDown();

        Plugin::unload('Translate');

        unset($this->AppHelper->request, $this->AppHelper, $this->View);
    }

    public function testUrlWithoutLocale()
    {
        $url = $this->AppHelper->url();
        $this->assertEquals($url, Router::url('/'));
    }

    public function testUrlWithLocale()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $url = $this->AppHelper->url(['locale' => 'por']);
        $this->assertEquals($url, Router::url('/por/index'));
    }

    public function testFullUrlWithLocale()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $url = $this->AppHelper->url(['locale' => 'por'], true);
        $this->assertEquals($url, Router::url('/por/index', true));
    }

    public function testUrlWithRequestParams()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->AppHelper->request->params['locale'] = 'por';
        $url = $this->AppHelper->url();
        $this->assertEquals($url, Router::url('/por/index'));
    }

    public function testFullUrlWithRequestParams()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->AppHelper->request->params['locale'] = 'por';
        $url = $this->AppHelper->url(null, true);
        $this->assertEquals($url, Router::url('/por/index', true));
    }
}
