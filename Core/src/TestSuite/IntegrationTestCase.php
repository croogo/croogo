<?php

namespace Croogo\Core\TestSuite;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Network\Request;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase as CakeIntegrationTestCase;
use Croogo\Core\Plugin;
use Croogo\Core\Event\EventManager;
use Croogo\Core\TestSuite\Constraint\EntityHasProperty;
use PHPUnit_Util_InvalidArgumentHelper;

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
     * @param \Croogo\Users\Model\Entity\User|\Cake\ORM\Query|string $user
     */
    public function user($user)
    {
        if (is_string($user)) {
            $user = TableRegistry::get('Croogo/Users.Users')
                ->findByUsername($user);
        }
        if ($user instanceof Query) {
            $user = $user->firstOrFail();
        }

        $this->session([
            'Auth' => [
                'User' => $user->toArray()
            ]
        ]);
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

    public function assertEntityHasProperty($propertyName, EntityInterface $entity, $message = '')
    {
        if (!is_string($propertyName)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'string');
        }

        if (!preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $propertyName)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'valid property name');
        }

        $constraint = new EntityHasProperty(
            $propertyName
        );

        static::assertThat($entity, $constraint, $message);
    }
}
