<?php

namespace Croogo\Core\TestSuite;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\TestSuite\TestCase as CakeTestCase;
use Cake\Utility\Hash;
use Croogo\Core\Configure\CroogoJsonReader;
use Croogo\Core\Plugin;
use Croogo\Core\Router;
use Croogo\Core\Event\EventManager;
use Croogo\Core\TestSuite\CroogoTestFixture;

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

    protected $_paths = [];

/**
 * Setup settings.json file for the test application. Tests not requiring
 * settings fixture can turn it off by setting this to false.
 */
    public $setupSettings = true;

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

        $appDir = Plugin::path('Croogo/Core') . 'tests' . DS . 'test_app' . DS;

//		App::build(array(
//			'Plugin' => array($appDir . 'Plugin' . DS),
//			'View' => array($appDir . 'View' . DS),
//		), App::PREPEND);
//		$this->_paths = App::paths();

        Plugin::unload('Croogo/Install');
        Plugin::load('Croogo/Example', ['autoload' => true, 'path' => '../Example/']);
        Configure::write('Acl.database', 'test');
        $this->setupSettings($appDir);

        $this->previousPlugins = Plugin::loaded();
    }

    public function setupSettings($appDir)
    {
        if (!$this->setupSettings) {
            return;
        }

//		$Setting = ClassRegistry::init('Settings.Setting');
//		$Setting->settingsPath = $appDir . 'Config' . DS . 'settings.json';
//		Configure::drop('settings');
//		Configure::config('settings', new CroogoJsonReader(dirname($Setting->settingsPath) . DS));
//		$Setting->writeConfiguration();
    }

    public function tearDown()
    {
        parent::tearDown();

//		App::build($this->_paths);

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
