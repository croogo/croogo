<?php

namespace Croogo\Core\Test\TestCase\View\Helper;

use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\View\Helper;
use Cake\View\View;
use Croogo\Core\PluginManager;
use Croogo\Core\TestSuite\CroogoTestCase;

class CroogoAppHelper extends Helper
{
}

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

    protected array $fixtures = [
//      'plugin.Croogo/Settings.Setting',
//      'plugin.Croogo/Taxonomy.Type',
//      'plugin.Croogo/Taxonomy.Vocabulary',
//      'plugin.Croogo/Taxonomy.TypesVocabulary',
    ];

    public function setUp(): void
    {
        parent::setUp();

        PluginManager::load('Croogo/Translate', ['autoload' => true, 'path' => '../Translate/']);

        $request = new ServerRequest();
        $this->View = new View($request, new Response());
        $this->AppHelper = new CroogoAppHelper($this->View);
        $this->AppHelper->getView()->setRequest($request);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        PluginManager::clear('Translate');

        unset($this->AppHelper->request, $this->AppHelper, $this->View);
    }

    public function testUrlWithoutLocale(): void
    {
        $url = $this->AppHelper->url();
        $this->assertEquals($url, Router::url('/'));
    }

    public function testUrlWithLocale(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $url = $this->AppHelper->url(['locale' => 'por']);
        $this->assertEquals($url, Router::url('/por/index'));
    }

    public function testFullUrlWithLocale(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $url = $this->AppHelper->url(['locale' => 'por'], true);
        $this->assertEquals($url, Router::url('/por/index', true));
    }

    public function testUrlWithRequestParams(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->AppHelper->request->params['locale'] = 'por';
        $url = $this->AppHelper->url();
        $this->assertEquals($url, Router::url('/por/index'));
    }

    public function testFullUrlWithRequestParams(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->AppHelper->request->params['locale'] = 'por';
        $url = $this->AppHelper->url(null, true);
        $this->assertEquals($url, Router::url('/por/index', true));
    }
}
