<?php

App::uses('CroogoTheme', 'Extensions.Lib');
App::uses('CroogoTestCase', 'Croogo.Lib/TestSuite');

class CroogoThemeTest extends CroogoTestCase {

/**
 * CroogoTheme class
 * @var CroogoTheme
 */
	public $CroogoTheme;

	public function setUp() {
		parent::setUp();
		$this->CroogoTheme = $this->getMock('CroogoTheme', null);
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->CroogoTheme);
	}

/**
 * testDeleteEmptyTheme
 * @expectedException InvalidArgumentException
 */
	public function testDeleteEmptyTheme() {
		$this->CroogoTheme->delete(null);
	}

/**
 * testDeleteBogusTheme
 * @expectedException UnexpectedValueException
 */
	public function testDeleteBogusTheme() {
		$this->CroogoTheme->delete('Bogus');
	}

}