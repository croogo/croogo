<?php

App::uses('CroogoTestCase', 'Croogo.TestSuite');
App::uses('CroogoJsonReader', 'Croogo.Configure');

class MockCroogoJsonReader extends CroogoJsonReader {

	public $written = null;

	public function getPath() {
		return $this->_path;
	}

}

class CroogoJsonReaderTest extends CroogoTestCase {

/**
 * setUp
 */
	public function setUp() {
		parent::setUp();
		$this->CroogoJsonReader = $this->getMock('MockCroogoJsonReader',
			null,
			array(CakePlugin::path('Croogo') . 'Test' . DS . 'test_app' . DS . 'Config' . DS)
			);
		$this->testFile = $this->CroogoJsonReader->getPath() . 'test.json';
	}

/**
 * tearDown
 */
	public function tearDown() {
		if (file_exists($this->testFile)) {
			unlink($this->testFile);
		}
	}

/**
 * testDefaultPath
 */
	public function testDefaultPath() {
		$path = $this->CroogoJsonReader->getPath();
		$this->assertEquals(CakePlugin::path('Croogo') . 'Test' . DS . 'test_app' . DS . 'Config' . DS, $path);
	}

/**
 * testRead
 */
	public function testRead() {
		$settings = $this->CroogoJsonReader->read('settings', 'settings');
		$expected = array(
			'acl_plugin' => 'Acl',
			'email' => 'you@your-site.com',
			'feed_url' => '',
			'locale' => 'eng',
			'status' => 1,
			'tagline' => 'A CakePHP powered Content Management System.',
			'theme' => '',
			'timezone' => 0,
			'title' => 'Croogo - Test',
		);
		$this->assertEquals($expected, $settings['Site']);
	}

/**
 * testDump
 */
	public function testDump() {
		$settings = array(
			'Site' => array(
				'title' => 'Croogo - Test (Edited)',
			),
			'Reading' => array(
				'date_time_format' => 'Y m d',
				'nodes_per_page' => 20,
			),
			'Nested' => array(
				'StringValue' => 'Is Fine',
				'AnotherArray' => array(
					'should' => 'be',
					'persisted' => 'correctly',
				),
			),
			'Hook' => array(
				'someKey' => 'value',
				'model_properties' => array('ignored', 'to', 'oblivion'),
				'controller_properties' => array('ignored', 'to', 'oblivion'),
			),
		);
		$this->CroogoJsonReader->dump(basename($this->testFile), $settings);
		$expected = <<<END
{
\s+"Site": {
\s+"title": "Croogo - Test \(Edited\)"
\s+},
\s+"Reading": {
\s+"date_time_format": "Y m d",
\s+"nodes_per_page": 20
\s+},
\s+"Nested": {
\s+"StringValue": "Is Fine",
\s+"AnotherArray": {
\s+"should": "be",
\s+"persisted": "correctly"
\s+}
\s+},
\s+"Hook": {
\s+"someKey": "value"
\s+}
}
END;
		$this->assertRegExp($expected, file_get_contents($this->testFile));
	}

}
