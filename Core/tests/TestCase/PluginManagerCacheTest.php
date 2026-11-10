<?php
declare(strict_types=1);

namespace Croogo\Core\Test\TestCase;

use Cake\Cache\Cache;
use Cake\Cache\Engine\ArrayEngine;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\TestSuite\TestCase;
use Croogo\Core\PluginManager;
use Croogo\Core\Test\TestSuite\DeleteSpyEngine;

/**
 * Regression tests for the EventHandlers cache invalidation.
 *
 * PluginManager::load() runs on every request, so it must never drop the
 * listener map; only the activation lifecycle may, and exactly once.
 */
class PluginManagerCacheTest extends TestCase
{
    protected const PLUGIN = 'Shops';

    /**
     * @var array|null
     */
    protected $cachedSettingsConfig;

    /**
     * @var array|null
     */
    protected $croogoMenusConfig;

    /**
     * @var array|null
     */
    protected $pluginsConfig;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->_unloadTestPlugin();

        // Without a registered path, PluginManager::path() falls back to
        // PluginCollection::get(), which loads the plugin as a side effect.
        $this->pluginsConfig = Configure::read('plugins');
        Configure::write('plugins.' . static::PLUGIN, ROOT . DS . 'plugins' . DS . static::PLUGIN . DS);

        $this->cachedSettingsConfig = Cache::getConfig('cached_settings');
        Cache::drop('cached_settings');
        Cache::setConfig('cached_settings', [
            'className' => DeleteSpyEngine::class,
            'groups' => ['settings'],
        ]);

        // activate()/deactivate() also clear croogo_menus, which the test
        // bootstrap does not configure.
        $this->croogoMenusConfig = Cache::getConfig('croogo_menus');
        Cache::drop('croogo_menus');
        Cache::setConfig('croogo_menus', ['className' => ArrayEngine::class]);

        Cache::write('EventHandlers', [['plugin' => static::PLUGIN]], 'cached_settings');
        Cache::write('pluginDeps', ['dependencies' => [], 'usedBy' => []], 'cached_settings');
        DeleteSpyEngine::$deletedKeys = [];
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        $this->_unloadTestPlugin();

        Cache::drop('cached_settings');
        if ($this->cachedSettingsConfig) {
            Cache::setConfig('cached_settings', $this->cachedSettingsConfig);
        }
        Cache::drop('croogo_menus');
        if ($this->croogoMenusConfig) {
            Cache::setConfig('croogo_menus', $this->croogoMenusConfig);
        }
        DeleteSpyEngine::$deletedKeys = [];
        Configure::write('plugins', $this->pluginsConfig);

        parent::tearDown();
    }

    /**
     * clear() only detaches listeners, it does not unload the plugin
     *
     * @return void
     */
    protected function _unloadTestPlugin(): void
    {
        if (!Plugin::isLoaded(static::PLUGIN)) {
            return;
        }
        PluginManager::clear(static::PLUGIN);
        PluginManager::getCollection()->remove(static::PLUGIN);
    }

    /**
     * PluginManager with the settings-table writes stubbed out, so the
     * activation lifecycle can be exercised without a database.
     *
     * @return \Croogo\Core\PluginManager
     */
    protected function _pluginManager(): PluginManager
    {
        return new class extends PluginManager {
            /**
             * @param string $plugin Plugin name
             * @return void
             */
            public function addBootstrap($plugin): void
            {
            }

            /**
             * @param string $plugin Plugin name
             * @return void
             */
            public function removeBootstrap($plugin): void
            {
            }

            /**
             * @param string $plugin Plugin name
             * @return null
             */
            public function getActivator($plugin = null)
            {
                return null;
            }
        };
    }

    /**
     * A plain request loads every active plugin - none of that may touch the
     * cached listener map.
     *
     * @return void
     */
    public function testLoadKeepsEventHandlersCache(): void
    {
        PluginManager::load(static::PLUGIN, ['autoload' => true, 'events' => true]);

        $this->assertSame([], DeleteSpyEngine::$deletedKeys);
        $this->assertNotEmpty(Cache::read('EventHandlers', 'cached_settings'));
    }

    /**
     * @return void
     */
    public function testActivateInvalidatesEventHandlersCacheOnce(): void
    {
        $manager = $this->_pluginManager();

        $this->assertTrue($manager->activate(static::PLUGIN));
        $this->assertSame(['EventHandlers'], DeleteSpyEngine::$deletedKeys);
    }

    /**
     * deactivate() delegates to clear(), which is the single invalidation point
     *
     * @return void
     */
    public function testDeactivateInvalidatesEventHandlersCacheOnce(): void
    {
        PluginManager::load(static::PLUGIN, ['autoload' => true]);
        DeleteSpyEngine::$deletedKeys = [];

        $manager = $this->_pluginManager();

        $this->assertTrue($manager->deactivate(static::PLUGIN));
        $this->assertSame(['EventHandlers'], DeleteSpyEngine::$deletedKeys);
    }

    /**
     * @return void
     */
    public function testInvalidationLeavesOtherCachedSettingsKeysAlone(): void
    {
        $manager = $this->_pluginManager();
        $manager->activate(static::PLUGIN);
        $manager->deactivate(static::PLUGIN);

        $this->assertNotContains('pluginDeps', DeleteSpyEngine::$deletedKeys);
        $this->assertNotEmpty(Cache::read('pluginDeps', 'cached_settings'));
    }

    /**
     * During installation cached_settings is not registered yet - deleting from
     * an unknown cache config would throw.
     *
     * @return void
     */
    public function testClearWithoutCachedSettingsConfigDoesNotThrow(): void
    {
        Cache::drop('cached_settings');

        PluginManager::clear(static::PLUGIN);

        $this->assertNotContains('cached_settings', Cache::configured());
    }
}
