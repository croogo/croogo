<?php

namespace Croogo\Core\TestSuite;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Http\ServerRequest;
use Cake\ORM\Query;
use Cake\TestSuite\TestCase as CakeTestCase;
use Croogo\Core\Event\EventManager;
use Croogo\Core\PluginManager;
use Croogo\Core\TestSuite\Constraint\QueryCount;
use InvalidArgumentException;

/**
 * CroogoTestCase class
 *
 * @category TestSuite
 * @package  Croogo
 * @version  1.4
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TestCase extends CakeTestCase
{
    protected $previousPlugins = [];

    public static function setUpBeforeClass(): void
    {
        Configure::write('Config.language', 'eng');
    }

    public static function tearDownAfterClass(): void
    {
        Configure::write('Config.language', Configure::read('Site.locale'));
    }

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        EventManager::instance(new EventManager);
        Configure::write('EventHandlers', []);

        PluginManager::clear('Croogo/Install');
        Configure::write('Acl.database', 'test');

        $this->previousPlugins = Plugin::loaded();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        // Unload all plugins that were loaded while running tests
        $diff = array_diff(Plugin::loaded(), $this->previousPlugins);
        foreach ($diff as $plugin) {
            PluginManager::clear($plugin);
        }
    }

    public function assertQueryCount($count, \Cake\ORM\Query\SelectQuery $query, $message = '')
    {
        if (!is_int($count)) {
            throw new InvalidArgumentException('Argument 1 must be of type integer.');
        }

        $constraint = new QueryCount($count);

        static::assertThat($query, $constraint, $message);
    }

    /**
     * Helper method to create an test API request (with the appropriate detector)
     */
    protected function _apiRequest($params)
    {
        ServerRequest::addDetector('api', [
            'callback' => ['Croogo\\Core\\Router', 'isApiRequest'],
        ]);

        return new ServerRequest(['params' => $params]);
    }
}
