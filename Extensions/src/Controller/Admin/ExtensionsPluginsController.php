<?php

namespace Croogo\Extensions\Controller\Admin;

use Cake\Event\Event;
use Croogo\Extensions\CroogoPlugin;
use Croogo\Extensions\ExtensionsInstaller;
use Cake\Core\Exception\Exception;

/**
 * Extensions Plugins Controller
 *
 * @category Controller
 * @package  Croogo.Extensions.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExtensionsPluginsController extends AppController
{

/**
 * BC compatibility
 */
    public function __get($name)
    {
        if ($name == 'corePlugins') {
            return $this->_CroogoPlugin->corePlugins;
        }
    }

/**
 * beforeFilter
 *
 * @return void
 */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->_CroogoPlugin = new CroogoPlugin();
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
        $this->set('corePlugins', $this->_CroogoPlugin->corePlugins);
        $this->set('bundledPlugins', $this->_CroogoPlugin->bundledPlugins);
        $this->set(compact('plugins'));
    }

/**
 * Admin add
 *
 * @return void
 */
    public function add()
    {
        $this->set('title_for_layout', __d('croogo', 'Upload a new plugin'));

        if (!empty($this->request->data)) {
            $file = $this->request->data['Plugin']['file'];
            unset($this->request->data['Plugin']['file']);

            $Installer = new ExtensionsInstaller;
            try {
                $Installer->extractPlugin($file['tmp_name']);
            } catch (CakeException $e) {
                $this->Flash->error($e->getMessage());
                return $this->redirect(['action' => 'add']);
            }
            return $this->redirect(['action' => 'index']);
        }
    }

/**
 * Admin delete
 *
 * @return void
 */
    public function delete($id)
    {
        $plugin = $this->request->query('name');
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
 * @return void
 */
    public function toggle()
    {
        $plugin = $this->request->query('name');
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
 * @return void
 */
    public function migrate()
    {
        $plugin = $this->request->query('name');
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
 * @throws CakeException
 */
    public function moveup()
    {
        $plugin = $this->request->query('name');
        $this->request->allowMethod('post');

        if ($plugin === null) {
            throw new Exception(__d('croogo', 'Invalid plugin'));
        }

        $class = 'success';
        $result = $this->_CroogoPlugin->move('up', $plugin);
        if ($result === true) {
            $message = __d('croogo', 'Plugin %s has been moved up', $plugin);
        } else {
            $message = $result;
            $class = 'error';
        }
        $this->Flash->set($message, ['params' => compact('class')]);

        return $this->redirect($this->referer());
    }

/**
 * Move down a plugin in bootstrap order
 *
 * @throws CakeException
 */
    public function movedown()
    {
        $plugin = $this->request->query('name');
        $this->request->allowMethod('post');

        if ($plugin === null) {
            throw new Exception(__d('croogo', 'Invalid plugin'));
        }

        $class = 'success';
        $result = $this->_CroogoPlugin->move('down', $plugin);
        if ($result === true) {
            $message = __d('croogo', 'Plugin %s has been moved down', $plugin);
        } else {
            $message = $result;
            $class = 'error';
        }
        $this->Flash->set($message, ['params' => compact('class')]);

        return $this->redirect($this->referer());
    }
}
