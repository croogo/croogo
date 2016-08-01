<?php

namespace Croogo\Core\TestSuite;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\TestSuite\TestCase as CakeTestCase;
use Croogo\Core\Plugin;
use Croogo\Core\Event\EventManager;

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

    public static function setUpBeforeClass()
    {
        Configure::write('Config.language', 'eng');
    }

    public static function tearDownAfterClass()
    {
        Configure::write('Config.language', Configure::read('Site.locale'));
    }

/**
 * setUp
 *
 * @return void
 */
    public function setUp()
    {
        parent::setUp();

        EventManager::instance(new EventManager);
        Configure::write('EventHandlers', []);

        Plugin::unload('Croogo/Install');
        Plugin::load('Croogo/Example', ['autoload' => true, 'path' => '../Example/']);
        Configure::write('Acl.database', 'test');

        $this->previousPlugins = Plugin::loaded();
    }

    public function tearDown()
    {
        parent::tearDown();

        // Unload all plugins that were loaded while running tests
        Plugin::unload(array_diff(Plugin::loaded(), $this->previousPlugins));
    }

/**
 * Helper method to create an test API request (with the appropriate detector)
 */
    protected function _apiRequest($params)
    {
        $request = new Request();
        $request->addParams($params);
        $request->addDetector('api', [
            'callback' => ['Croogo\\Core\\Router', 'isApiRequest'],
        ]);
        return $request;
    }
}
