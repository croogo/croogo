<?php
namespace Croogo\Blocks\Test\TestCase;

use Croogo\TestSuite\CroogoTestCase;

class AllBlocksTestsTest extends PHPUnit_Framework_TestSuite
{

/**
 * suite
 *
 * @return CakeTestSuite
 */
    public static function suite()
    {
        $suite = new CakeTestSuite('All Blocks tests');
        $suite->addTestDirectoryRecursive(Plugin::path('Blocks') . 'Test' . DS . 'Case' . DS);
        return $suite;
    }
}
