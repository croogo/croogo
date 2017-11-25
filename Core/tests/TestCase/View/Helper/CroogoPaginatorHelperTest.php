<?php
namespace Croogo\Core\Test\TestCase\View\Helper;

use Cake\Controller\Controller;
use Cake\Network\Request;
use Cake\View\Helper\HtmlHelper;
use Cake\View\View;
use Croogo\Core\TestSuite\CroogoTestCase;
use Croogo\Core\View\Helper\CroogoPaginatorHelper;

class CroogoPaginatorHelperTest extends CroogoTestCase
{

    public $fixtures = [
//		'plugin.taxonomy.type',
    ];

    public function setUp()
    {
        $controller = null;
        $this->View = new View($controller);
        $this->Paginator = new CroogoPaginatorHelper($this->View);
        $this->Paginator->request = new Request();
        $this->Paginator->Html = new HtmlHelper($this->View);
    }

    public function tearDown()
    {
        unset($this->View, $this->Paginator);
    }

    public function testPrev()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->Paginator->request->params['paging'] = [
            'Test' => [
                'page' => 3,
                'prevPage' => true,
                'nextPage' => true,
                'current' => 1,
                'count' => 5,
                'pageCount' => 5,
                'options' => ['page' => 1],
                'paramType' => 'named'
            ]];

            $result = $this->Paginator->prev();
            $this->assertContains('</li>', $result);
    }

    public function testNext()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->Paginator->request->params['paging'] = [
            'Test' => [
                'page' => 3,
                'prevPage' => true,
                'nextPage' => true,
                'current' => 1,
                'count' => 5,
                'pageCount' => 5,
                'options' => ['page' => 1],
                'paramType' => 'named'
            ]];

            $result = $this->Paginator->next();
            $this->assertContains('</li>', $result);
    }

    public function testFirst()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->Paginator->request->params['paging'] = [
            'Test' => [
                'page' => 3,
                'prevPage' => true,
                'nextPage' => true,
                'current' => 1,
                'count' => 5,
                'pageCount' => 5,
                'options' => ['page' => 1],
                'paramType' => 'named'
            ]];

            $result = $this->Paginator->first();
            $this->assertContains('</li>', $result);
    }

    public function testLast()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->Paginator->request->params['paging'] = [
            'Test' => [
                'page' => 3,
                'prevPage' => true,
                'nextPage' => true,
                'current' => 1,
                'count' => 5,
                'pageCount' => 5,
                'options' => ['page' => 1],
                'paramType' => 'named'
            ]];

            $result = $this->Paginator->last();
            $this->assertContains('</li>', $result);
    }

    public function testNumbersFewPages()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->Paginator->request->params['paging'] = [
            'Test' => [
                'page' => 3,
                'current' => 1,
                'count' => 5,
                'pageCount' => 5,
                'options' => ['page' => 1],
                'paramType' => 'named'
            ]];
            $result = $this->Paginator->numbers();
            $this->assertContains('>1</a>', $result);
            $this->assertContains('>2</a>', $result);
            $this->assertContains('class="active">3</a>', $result);
    }

    public function testNumbersManyPages()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->Paginator->request->params['paging'] = [
            'Test' => [
                'page' => 25,
                'current' => 1,
                'count' => 30,
                'pageCount' => 30,
                'options' => ['page' => 1],
                'paramType' => 'named'
            ]];
            $result = $this->Paginator->numbers();
            $this->assertContains('>21</a>', $result);
            $this->assertContains('>28</a>', $result);
            $this->assertContains('class="active">25</a>', $result);
    }

    public function testNumbersPageEqualsEnd()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->Paginator->request->params['paging'] = [
            'Test' => [
                'page' => 30,
                'current' => 1,
                'count' => 30,
                'pageCount' => 30,
                'options' => ['page' => 1],
                'paramType' => 'named'
            ]];
            $result = $this->Paginator->numbers();
            $this->assertContains('>22</a>', $result);
            $this->assertContains('class="active">30</a>', $result);
    }
}
