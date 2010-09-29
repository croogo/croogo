<?php
/**
 * Extensions Plugins Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExtensionsPluginsController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    public $name = 'ExtensionsPlugins';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    public $uses = array(
        'Setting',
        'User',
    );
/**
 * Core plugins
 *
 * @var array
 * @access public
 */
    public $corePlugins = array(
        'acl',
        'extensions',
    );

    public function beforeFilter() {
        parent::beforeFilter();

        App::import('Core', 'File');
        APP::import('Core', 'Folder');
    }

    public function admin_index() {
        $this->set('title_for_layout', __('Plugins', true));

        $pluginAliases = $this->Croogo->getPlugins();
        $plugins = array();
        foreach ($pluginAliases AS $pluginAlias) {
            $plugins[$pluginAlias] = $this->Croogo->getPluginData($pluginAlias);
        }
        $this->set('corePlugins', $this->corePlugins);
        $this->set(compact('plugins'));
    }

    public function admin_add() {
        $this->set('title_for_layout', __('Upload a new plugin', true));

        if (!empty($this->data)) {
            $file = $this->data['Plugin']['file'];
            unset($this->data['Plugin']['file']);

            // get plugin name and root
            $zip = zip_open($file['tmp_name']);
            $root = 0;
            if ($zip) {
                while ($zip_entry = zip_read($zip)) {
                    $zipEntryName = zip_entry_name($zip_entry);
                    $searches = array('activation', 'bootstrap', 'routes', 'app_controller', 'app_model', 'app_helper');
                    foreach ($searches AS $search) { 
                        if (preg_match('/([A-Za-z0-9_]+)_'.$search.'\.php/', $zipEntryName, $matches)) {
                            $plugin = $matches[1];
                            foreach (explode('/', $zipEntryName) as $folder) {
                                if (in_array($folder, array(
                                    'config',
                                    $plugin.'_app_controller.php',
                                    $plugin.'_app_model.php',
                                    $plugin.'_app_helper.php'
                                    ))) {
                                    break;
                                }
                                $root++;
                            }
                            break;
                        }
                    }
                }
            }
            zip_close($zip);

            if (!$plugin) {
                $this->Session->setFlash(__('Invalid plugin.', true), 'default', array('class' => 'error'));
                $this->redirect(array('action' => 'add'));
            }

            $pluginName = $plugin;

            if (is_dir(APP . 'plugins' . DS . $pluginName)) {
                $this->Session->setFlash(__('Plugin already exists.', true), 'default', array('class' => 'error'));
                $this->redirect(array('action' => 'add'));
            }

            // extract
            $zip = zip_open($file['tmp_name']);
            if ($zip) {
                // create root plugin dir
                $path = APP . 'plugins' . DS . $pluginName . DS;
                mkdir($path);
                while ($zip_entry = zip_read($zip)) {
                    $zipEntryName = zip_entry_name($zip_entry);
                    $zipEntryNameE = array_slice(explode('/', $zipEntryName), $root);
                    if (!empty($zipEntryNameE[count($zipEntryNameE)-1])) {
                        $path = APP . 'plugins' . DS . $pluginName . DS . implode(DS, $zipEntryNameE);
                    } else {
                        $path = APP . 'plugins' . DS . $pluginName . DS . implode(DS, $zipEntryNameE) . DS;
                    }
                    if (substr($path, strlen($path) - 1) == DS) {
                        // create directory
                        if (!is_dir($path)) {
                            mkdir($path);
                        }
                    } else {
                        // create file
                        if (zip_entry_open($zip, $zip_entry, 'r')) {
                            $fileContent = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                            touch($path);
                            $fh = fopen($path, 'w');
                            fwrite($fh, $fileContent);
                            fclose($fh);
                            zip_entry_close($zip_entry);
                        }
                    }
                }
            }
            zip_close($zip);

            $this->redirect(array('action' => 'index'));
        }
    }

    public function admin_delete($plugin = null) {
        if (!$plugin) {
            $this->Session->setFlash(__('Invalid plugin', true), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }
        if ($this->Croogo->pluginIsActive($plugin)) {
            $this->Session->setFlash(__('You cannot delete a plugin that is currently active.', true), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
        }

        $folder =& new Folder;
        if ($folder->delete(APP . 'plugins' . DS . $plugin)) {
            $this->Session->setFlash(__('Plugin deleted successfully.', true), 'default', array('class' => 'success'));
        } else {
            $this->Session->setFlash(__('Plugin could not be deleted.', true), 'default', array('class' => 'error'));
        }

        $this->redirect(array('action' => 'index'));
    }

    public function admin_toggle($plugin = null) {
        if (!$plugin) {
            $this->Session->setFlash(__('Invalid plugin', true), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }
        $className = Inflector::camelize($plugin) . 'Activation';
        if (App::import('Plugin', $className)) {
            $pluginActivation = new $className;
        }
        
        if ($this->Croogo->pluginIsActive($plugin)) {
            if (!isset($pluginActivation) || 
                (isset($pluginActivation) && method_exists($pluginActivation, 'beforeDeactivation') && $pluginActivation->beforeDeactivation($this))) {
                $this->Croogo->removePluginBootstrap($plugin);
                if (isset($pluginActivation) && method_exists($pluginActivation, 'onDeactivation')) {
                    $pluginActivation->onDeactivation($this);
                }
                $this->Session->setFlash(__('Plugin deactivated successfully.', true), 'default', array('class' => 'success'));
            } else {
                $this->Session->setFlash(__('Plugin could not be deactivated. Please, try again.', true), 'default', array('class' => 'error'));
            }
        } else {
            if (!isset($pluginActivation) ||
                (isset($pluginActivation) && method_exists($pluginActivation, 'beforeActivation') && $pluginActivation->beforeActivation($this))) {
                $this->Croogo->addPluginBootstrap($plugin);
                if (isset($pluginActivation) && method_exists($pluginActivation, 'onActivation')) {
                    $pluginActivation->onActivation($this);
                }
                $this->Session->setFlash(__('Plugin activated successfully.', true), 'default', array('class' => 'success'));
            } else {
                $this->Session->setFlash(__('Plugin could not be activated. Please, try again.', true), 'default', array('class' => 'error'));
            }
        }
        $this->redirect(array('action' => 'index'));
    }

}
?>