<?php

namespace Croogo\Core\TestSuite;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\TestSuite\IntegrationTestCase as CakeIntegrationTestCase;
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
class IntegrationTestCase extends CakeIntegrationTestCase
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

        Plugin::routes();
        Plugin::events();
        EventManager::loadListeners();

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

    /**
     * Asserts flash message contents
     *
     * @param string $expected The expected contents.
     * @param string $key
     * @param int $index
     * @param string $message The failure message that will be appended to the generated message.
     */
    public function assertFlash($expected, $key = 'flash', $index = 0, $message = '')
    {
        $this->assertSession($expected, 'Flash.' . $key . '.' . $index . '.message', 'Flash message did not match. ' . $message);
    }
}
