<?php

namespace Croogo\Extensions\Test\TestCase;
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

/**
 * testGetThemes
 */
	public function testGetThemes() {
		$themes = $this->CroogoTheme->getThemes();
		$this->assertTrue(array_key_exists('Mytheme', $themes));
	}

/**
 * testGetDataBogusTheme
 */
	public function testGetDataBogusTheme() {
		$data = $this->CroogoTheme->getData('BogusTheme');
		$this->assertSame(array(), $data);
	}

/**
 * testGetDataMixedManifest
 */
	public function testGetDataMixedManifest() {
		$data = $this->CroogoTheme->getData('MixedManifest');

		$keys = array_keys($data);
		sort($keys);

		$expected = array('name', 'regions', 'screenshot', 'type', 'vendor');
		$this->assertEquals($expected, $keys);

		$this->assertEquals('MixedManifest', $data['name']);
		$this->assertEquals('croogo/mixed-manifest-theme', $data['vendor']);
	}

}