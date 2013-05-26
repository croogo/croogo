<?php

App::uses('TaxonomiesHelper', 'Taxonomy.View/Helper');
App::uses('Controller', 'Controller');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class TheTaxonomyTestController extends Controller {

	public $uses = null;

}

class TaxonomiesHelperTest extends CroogoTestCase {

/**
 * setUp
 */
	public function setUp() {
		parent::setUp();
		$this->ComponentCollection = new ComponentCollection();

		$request = $this->getMock('CakeRequest');
		$response = $this->getMock('CakeResponse');
		$this->View = new View(new TheTaxonomyTestController($request, $response));
		$this->Taxonomies = new TaxonomiesHelper($this->View);
	}

/**
 * tearDown
 */
	public function tearDown() {
		unset($this->View);
		unset($this->Taxonomies);
	}

/**
 * Test [vocabulary] shortcode
 */
	public function testVocabularyShortcode() {
		$content = '[vocabulary:categories type="blog"]';
		$this->View->viewVars['vocabularies_for_layout']['categories'] = array(
			'Vocabulary' => array(
				'id' => 1,
				'title' => 'Categories',
				'alias' => 'categories',
			),
			'threaded' => array(),
		);
		Croogo::dispatchEvent('Helper.Layout.beforeFilter', $this->View, array('content' => &$content));
		$this->assertContains('vocabulary-1', $content);
		$this->assertContains('class="vocabulary"', $content);
	}

}
