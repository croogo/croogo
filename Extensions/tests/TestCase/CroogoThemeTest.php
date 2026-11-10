<?php

namespace Croogo\Extensions\Test\TestCase;

use Croogo\Core\TestSuite\CroogoTestCase;
use Croogo\Extensions\CroogoTheme;

class CroogoThemeTest extends CroogoTestCase
{

    /**
     * CroogoTheme class
     * @var CroogoTheme
     */
    public $CroogoTheme;

    public function setUp(): void
    {
        parent::setUp();
        $this->CroogoTheme = $this->getMock('CroogoTheme', null);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->CroogoTheme);
    }

    /**
     * testDeleteEmptyTheme
     * @expectedException InvalidArgumentException
     */
    public function testDeleteEmptyTheme(): void
    {
        $this->CroogoTheme->delete(null);
    }

    /**
     * testDeleteBogusTheme
     * @expectedException UnexpectedValueException
     */
    public function testDeleteBogusTheme(): void
    {
        $this->CroogoTheme->delete('Bogus');
    }

    /**
     * testGetThemes
     */
    public function testGetThemes(): void
    {
        $themes = $this->CroogoTheme->getThemes();
        $this->assertTrue(array_key_exists('Mytheme', $themes));
    }

    /**
     * testGetDataBogusTheme
     */
    public function testGetDataBogusTheme(): void
    {
        $data = $this->CroogoTheme->getData('BogusTheme');
        $this->assertSame([], $data);
    }

    /**
     * testGetDataMixedManifest
     */
    public function testGetDataMixedManifest(): void
    {
        $data = $this->CroogoTheme->getData('MixedManifest');

        $keys = array_keys($data);
        sort($keys);

        $expected = ['name', 'regions', 'screenshot', 'settings', 'type', 'vendor'];
        $this->assertEquals($expected, $keys);

        $this->assertEquals('MixedManifest', $data['name']);
        $this->assertEquals('croogo/mixed-manifest-theme', $data['vendor']);
    }
}
