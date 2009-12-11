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
    var $name = 'ExtensionsPlugins';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    var $uses = array(
        'Setting',
        'User',
    );
/**
 * Core plugins
 *
 * @var array
 * @access public
 */
    var $corePlugins = array(
        'acl',
        'extensions',
    );

    function beforeFilter() {
        parent::beforeFilter();

        App::import('Core', 'File');
        APP::import('Core', 'Folder');
    }

    function admin_index() {
        $this->pageTitle = __('Plugins', true);

        $folder =& new Folder;
        $folder->path = APP . 'plugins';
        $content = $folder->read();
        $plugins = $content['0'];
        $this->set('corePlugins', $this->corePlugins);
        $this->set(compact('content', 'plugins'));
    }

    function admin_add() {
        $this->pageTitle = __('Upload a new plugin', true);

        if (!empty($this->data)) {
            $file = $this->data['Plugin']['file'];
            unset($this->data['Plugin']['file']);

            // get plugin name
            $zip = zip_open($file['tmp_name']);
            $plugin = null;
            if ($zip) {
                while ($zip_entry = zip_read($zip)) {
                    $zipEntryName = zip_entry_name($zip_entry);
                    $searches = array('controllers', 'models', 'views');
                    foreach ($searches AS $search) {
                        if (strstr($zipEntryName, $search)) {
                            $zipEntryNameE = explode('/' . $search, $zipEntryName);
                            if (isset($zipEntryNameE['0'])) {
                                $pathE = explode('/', $zipEntryNameE['0']);
                                if (isset($pathE[count($pathE) - 1])) {
                                    $plugin = $pathE[count($pathE) - 1];
                                }
                            }
                        }
                    }
                }
            }
            zip_close($zip);

            if (!$plugin) {
                $this->Session->setFlash(__('Invalid plugin.', true));
                $this->redirect(array('action' => 'add'));
            }

            if (is_dir(APP . 'plugins' . DS . $plugin)) {
                $this->Session->setFlash(__('Plugin already exists.', true));
                $this->redirect(array('action' => 'add'));
            }

            // extract
            $zip = zip_open($file['tmp_name']);
            if ($zip) {
                while ($zip_entry = zip_read($zip)) {
                    $zipEntryName = zip_entry_name($zip_entry);
                    if (strstr($zipEntryName, $plugin . '/')) {
                        $zipEntryNameE = explode($plugin . '/', $zipEntryName);
                        if (isset($zipEntryNameE['1'])) {
                            $path = APP . 'plugins' . DS . $plugin . DS . str_replace('/', DS, $zipEntryNameE['1']);
                        } else {
                            $path = APP . 'plugins' . DS . $plugin . DS;
                        }

                        if (substr($path, strlen($path) - 1) == DS) {
                            // create directory
                            mkdir($path);
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
            }
            zip_close($zip);

            $this->redirect(array('action' => 'index'));
        }
    }

    function admin_delete($plugin = null) {
        if (!$plugin) {
            $this->Session->setFlash(__('Invalid plugin', true));
            $this->redirect(array('action' => 'index'));
        }

        $folder =& new Folder;
        if ($folder->delete(APP . 'plugins' . DS . $plugin)) {
            $this->Session->setFlash(__('Plugin deleted successfully.', true));
        } else {
            $this->Session->setFlash(__('Plugin could not be deleted.', true));
        }

        $this->redirect(array('action' => 'index'));
    }

}
?>