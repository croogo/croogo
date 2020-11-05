<?php
declare(strict_types=1);

namespace Croogo\Extensions\Controller\Admin;

use Cake\Core\Exception\Exception;
use Cake\Event\EventInterface;
use Croogo\Core\PluginManager;
use Croogo\Extensions\ExtensionsInstaller;

/**
 * Extensions Plugins Controller
 *
 * @category Controller
 * @package  Croogo.Extensions.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 * @property \Croogo\Core\Controller\Component\CroogoComponent $Croogo
 * @property \Croogo\Meta\Controller\Component\MetaComponent $Meta
 * @property \Croogo\Blocks\Controller\Component\BlocksComponent $BlocksHook
 * @property \Croogo\Acl\Controller\Component\FilterComponent $Filter
 * @property \Acl\Controller\Component\AclComponent $Acl
 * @property \Croogo\Core\Controller\Component\ThemeComponent $Theme
 * @property \Croogo\Acl\Controller\Component\AccessComponent $Access
 * @property \Croogo\Settings\Controller\Component\SettingsComponent $SettingsComponent
 * @property \Croogo\Nodes\Controller\Component\NodesComponent $NodesHook
 * @property \Croogo\Menus\Controller\Component\MenuComponent $Menu
 * @property \Croogo\Users\Controller\Component\LoggedInUserComponent $LoggedInUser
 * @property \Croogo\Taxonomy\Controller\Component\TaxonomyComponent $Taxonomy
 * @property \Crud\Controller\Component\CrudComponent $Crud
 */
class PluginsController extends AppController
{

    /**
     * BC compatibility
     */
    public function __get($name)
    {
        if ($name == 'corePlugins') {
            return PluginManager::$corePlugins;
        }
    }

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->_CroogoPlugin = new PluginManager();
        $this->_CroogoPlugin->setController($this);
    }

    /**
     * Admin index
     *
     * @return void
     */
    public function index()
    {
        $this->set('title_for_layout', __d('croogo', 'Plugins'));

        $plugins = $this->_CroogoPlugin->plugins(false);
        $this->set('corePlugins', PluginManager::$corePlugins);
        $this->set('bundledPlugins', PluginManager::$bundledPlugins);
        $this->set(compact('plugins'));
    }

    /**
     * Admin add
     *
     * @return \Cake\Http\Response|void
     */
    public function add()
    {
        $this->set('title_for_layout', __d('croogo', 'Upload a new plugin'));

        if ($this->getRequest()->is('post')) {
            $data = $this->getRequest()->getData();
            $file = $data['Plugin']['file'];
            unset($data['Plugin']['file']);
            $this->request = $this->getRequest()->withParsedBody($data);

            $Installer = new ExtensionsInstaller;
            try {
                $Installer->extractPlugin($file['tmp_name']);
            } catch (Exception $e) {
                $this->Flash->error($e->getMessage());

                return $this->redirect(['action' => 'add']);
            }

            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Admin delete
     *
     * @return \Cake\Http\Response|void
     */
    public function delete($id)
    {
        $plugin = $this->getRequest()->query('name');
        if (!$plugin) {
            $this->Flash->error(__d('croogo', 'Invalid plugin'));

            return $this->redirect(['action' => 'index']);
        }
        if ($this->_CroogoPlugin->isActive($plugin)) {
            $this->Flash->error(__d('croogo', 'You cannot delete a plugin that is currently active.'));

            return $this->redirect(['action' => 'index']);
        }

        $result = $this->_CroogoPlugin->delete($plugin);
        if ($result === true) {
            $this->Flash->success(__d('croogo', 'Plugin "%s" deleted successfully.', $plugin));
        } elseif (!empty($result[0])) {
            $this->Flash->error($result[0]);
        } else {
            $this->Flash->error(__d('croogo', 'Plugin could not be deleted.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Admin toggle
     *
     * @return \Cake\Http\Response|void
     */
    public function toggle()
    {
        $plugin = $this->getRequest()->getQuery('name');
        if (!$plugin) {
            $this->Flash->error(__d('croogo', 'Invalid plugin'));

            return $this->redirect(['action' => 'index']);
        }

        if ($this->_CroogoPlugin->isActive($plugin)) {
            $usedBy = $this->_CroogoPlugin->usedBy($plugin);
            if ($usedBy !== false) {
                $this->Flash->error(__d('croogo', 'Plugin "%s" could not be deactivated since "%s" depends on it.', $plugin, implode(', ', $usedBy)));

                return $this->redirect(['action' => 'index']);
            }
            $result = $this->_CroogoPlugin->deactivate($plugin);
            if ($result === true) {
                $this->Flash->success(__d('croogo', 'Plugin "%s" deactivated successfully.', $plugin));
            } elseif (is_string($result)) {
                $this->Flash->error($result);
            } else {
                $this->Flash->error(__d('croogo', 'Plugin could not be deactivated. Please, try again.'));
            }
        } else {
            $result = $this->_CroogoPlugin->activate($plugin);
            if ($result === true) {
                $this->Flash->success(__d('croogo', 'Plugin "%s" activated successfully.', $plugin));
            } elseif (is_string($result)) {
                $this->Flash->error($result);
            } else {
                $this->Flash->error(__d('croogo', 'Plugin could not be activated. Please, try again.'));
            }
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Migrate a plugin (database)
     *
     * @return \Cake\Http\Response|void
     */
    public function migrate()
    {
        $plugin = $this->getRequest()->query('name');
        if (!$plugin) {
            $this->Flash->error(__d('croogo', 'Invalid plugin'));
        } elseif ($this->_CroogoPlugin->migrate($plugin)) {
            $this->Flash->success(__d('croogo', 'Plugin "%s" migrated successfully.', $plugin));
        } else {
            $this->Flash->error(
                __d('croogo', 'Plugin "%s" could not be migrated. Error: %s', $plugin, implode('<br />', $this->_CroogoPlugin->migrationErrors))
            );
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Move up a plugin in bootstrap order
     *
     * @throws Exception
     */
    public function moveUp()
    {
        $plugin = $this->getRequest()->query('name');
        $this->getRequest()->allowMethod('post');

        if ($plugin === null) {
            throw new Exception(__d('croogo', 'Invalid plugin'));
        }

        $class = 'success';
        $result = $this->_CroogoPlugin->move('up', $plugin);
        if ($result === true) {
            $message = __d('croogo', 'Plugin %s has been moved up', $plugin);
            $this->Flash->success($message);
        } else {
            $message = $result;
            $this->Flash->error($message);
        }

        return $this->redirect($this->referer());
    }

    /**
     * Move down a plugin in bootstrap order
     *
     * @throws Exception
     */
    public function moveDown()
    {
        $plugin = $this->getRequest()->query('name');
        $this->getRequest()->allowMethod('post');

        if ($plugin === null) {
            throw new Exception(__d('croogo', 'Invalid plugin'));
        }

        $element = 'success';
        $result = $this->_CroogoPlugin->move('down', $plugin);
        if ($result === true) {
            $message = __d('croogo', 'Plugin %s has been moved down', $plugin);
        } else {
            $message = $result;
            $element = 'error';
        }
        $this->Flash->set($message, compact('element'));

        return $this->redirect($this->referer());
    }
}
