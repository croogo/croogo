<?php

namespace Croogo\Extensions\Test\TestCase\Utility;

use Croogo\TestSuite\CroogoTestCase;
use Extensions\Utility\DataMigration;

class DataMigrationTest extends CroogoTestCase
{

    public $fixtures = [
        'plugin.extensions.movie',
    ];

    protected $_sampleData = [
        [
            'Movie' => ['title' => 'Title One', 'year' => 2012],
        ],
        [
            'Movie' => ['title' => 'Title Two', 'year' => 2013],
        ],
    ];

    public function setUp()
    {
        $this->DataMigration = new DataMigration();
    }

    public function tearDown()
    {
        unset($this->DataMigration);
    }

/**
 * testData
 */
    public function testData()
    {
        $output = tempnam(TMP . 'tests', 'testData');

        $model = [
            'name' => 'Movie',
            'table' => 'movies',
            'ds' => 'test',
        ];

        $this->DataMigration->generate('all', [], [
            'model' => $model,
            'output' => $output,
        ]);
        $contents = file_get_contents($output);
        unlink($output);

        $this->assertContains('class MovieData {', $contents);
        $this->assertContains('public $table = \'movies\';', $contents);

        ClassRegistry::init('Movie')->saveAll($this->_sampleData);

        $this->DataMigration->generate('all', [], [
            'model' => $model,
            'output' => $output,
        ]);
        $contents = file_get_contents($output);
        unlink($output);

        foreach ($this->_sampleData as $movie) {
            $this->assertContains($movie['Movie']['title'], $contents);
        }
    }

/**
 * testLoad
 */
    public function testLoad()
    {
        $Movie = ClassRegistry::init('Movie');
        $Movie->saveAll($this->_sampleData);
        $sampleCount = count($this->_sampleData);
        $this->assertEquals($sampleCount, $Movie->find('count'));

        $output = TMP . 'tests' . DS . 'DataMigration' . DS . 'MovieData.php';
        $model = [
            'name' => 'Movie',
            'table' => 'movies',
            'ds' => 'test',
        ];

        $this->DataMigration->generate('all', [], [
            'model' => $model,
            'output' => $output,
        ]);
        $contents = file_get_contents($output);

        $Movie->getDataSource()->truncate($Movie);
        $this->assertEquals(0, $Movie->find('count'));

        $this->DataMigration->load(dirname($output), ['ds' => 'test']);
        unlink($output);
        $this->assertEquals($sampleCount, $Movie->find('count'));
        $this->assertFalse(ClassRegistry::getObject('MovieData'));
    }
}
