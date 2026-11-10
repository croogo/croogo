<?php

namespace Croogo\Core\TestSuite;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest as Request;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase as CakeTestCase;
use Croogo\Core\Event\EventManager;
use Croogo\Core\PluginManager;
use Croogo\Core\TestSuite\Constraint\EntityHasProperty;
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
// Cake 5: klasa IntegrationTestCase usunięta -> TestCase + IntegrationTestTrait
class IntegrationTestCase extends CakeTestCase
{
    use IntegrationTestTrait;

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
        if (!Plugin::isLoaded('Croogo/Example')) {
            PluginManager::load('Croogo/Example', ['autoload' => true, 'path' => '../Example/']);
        }
        Configure::write('Acl.database', 'test');

        PluginManager::events();
        EventManager::loadListeners();

        $this->previousPlugins = Plugin::loaded();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        // Unload all plugins that were loaded while running tests
        PluginManager::clear(array_diff(Plugin::loaded(), $this->previousPlugins));
    }

    /**
     * Helper method to create an test API request (with the appropriate detector)
     */
    protected function _apiRequest($params)
    {
        Request::addDetector('api', [
            'callback' => ['Croogo\\Core\\Router', 'isApiRequest'],
        ]);

        return new Request(['params' => $params]);
    }

    /**
     * @param \Croogo\Users\Model\Entity\User|\Cake\ORM\Query\SelectQuery|string $user
     */
    public function user($user): void
    {
        if (is_string($user)) {
            $user = \Cake\ORM\TableRegistry::getTableLocator()->get('Croogo/Users.Users')
                ->findByUsername($user);
        }
        if ($user instanceof \Cake\ORM\Query\SelectQuery) {
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
    public function assertFlash($expected, $key = 'flash', $index = 0, $message = ''): void
    {
        $this->assertSession(
            $expected,
            'Flash.' . $key . '.' . $index . '.message',
            'Flash message did not match. ' . $message
        );
    }

    public function assertEntityHasProperty($propertyName, EntityInterface $entity, $message = '')
    {
        if (!is_string($propertyName)) {
            throw new InvalidArgumentException('Argument 1 must be of type string.');
        }

        if (!preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $propertyName)) {
            throw new InvalidArgumentException('Argument 1 must be a valid property name.');
        }

        $constraint = new EntityHasProperty(
            $propertyName
        );

        static::assertThat($entity, $constraint, $message);
    }
}
