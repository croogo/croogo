<?php
App::uses('CroogoHelper', 'Croogo.View/Helper');
App::uses('CroogoPaginatorHelper', 'Croogo.View/Helper');
App::uses('Controller', 'Controller');
App::uses('CroogoTestCase', 'Croogo.TestSuite');
App::uses('View', 'View');
App::uses('HtmlHelper', 'View/Helper');

class CroogoPaginatorHelperTest extends CroogoTestCase {

	public function setUp() {
		$controller = null;
		$this->View = new View($controller);
		$this->Paginator = new CroogoPaginatorHelper($this->View);
		$this->Paginator->request = new CakeRequest(null, false);
		$this->Paginator->Html = new HtmlHelper($this->View);
	}

	public function tearDown() {
		unset($this->View, $this->Paginator);
	}

	public function testPrev() {
		$this->Paginator->request->params['paging'] = array(
			'Test' => array(
				'page' => 3,
				'prevPage' => true,
				'nextPage' => true,
				'current' => 1,
				'count' => 5,
				'pageCount' => 5,
				'options' => array('page' => 1),
				'paramType' => 'named'
		));

		$result = $this->Paginator->prev();
		$this->assertContains('</li>', $result);
	}

	public function testNext() {
		$this->Paginator->request->params['paging'] = array(
			'Test' => array(
				'page' => 3,
				'prevPage' => true,
				'nextPage' => true,
				'current' => 1,
				'count' => 5,
				'pageCount' => 5,
				'options' => array('page' => 1),
				'paramType' => 'named'
		));

		$result = $this->Paginator->next();
		$this->assertContains('</li>', $result);
	}

	public function testFirst() {
		$this->Paginator->request->params['paging'] = array(
			'Test' => array(
				'page' => 3,
				'prevPage' => true,
				'nextPage' => true,
				'current' => 1,
				'count' => 5,
				'pageCount' => 5,
				'options' => array('page' => 1),
				'paramType' => 'named'
		));

		$result = $this->Paginator->first();
		$this->assertContains('</li>', $result);
	}

	public function testLast() {
		$this->Paginator->request->params['paging'] = array(
			'Test' => array(
				'page' => 3,
				'prevPage' => true,
				'nextPage' => true,
				'current' => 1,
				'count' => 5,
				'pageCount' => 5,
				'options' => array('page' => 1),
				'paramType' => 'named'
		));

		$result = $this->Paginator->last();
		$this->assertContains('</li>', $result);
	}

	public function testNumbersFewPages() {
		$this->Paginator->request->params['paging'] = array(
			'Test' => array(
				'page' => 3,
				'current' => 1,
				'count' => 5,
				'pageCount' => 5,
				'options' => array('page' => 1),
				'paramType' => 'named'
		));
		$result = $this->Paginator->numbers();
		$this->assertContains('>1</a>', $result);
		$this->assertContains('>2</a>', $result);
		$this->assertContains('class="active">3</a>', $result);
	}

	public function testNumbersManyPages() {
		$this->Paginator->request->params['paging'] = array(
			'Test' => array(
				'page' => 25,
				'current' => 1,
				'count' => 30,
				'pageCount' => 30,
				'options' => array('page' => 1),
				'paramType' => 'named'
		));
		$result = $this->Paginator->numbers();
		$this->assertContains('>21</a>', $result);
		$this->assertContains('>28</a>', $result);
		$this->assertContains('class="active">25</a>', $result);
	}

	public function testNumbersPageEqualsEnd() {
		$this->Paginator->request->params['paging'] = array(
			'Test' => array(
				'page' => 30,
				'current' => 1,
				'count' => 30,
				'pageCount' => 30,
				'options' => array('page' => 1),
				'paramType' => 'named'
		));
				$result = $this->Paginator->numbers();
		$this->assertContains('>22</a>', $result);
		$this->assertContains('class="active">30</a>', $result);
	}

}
