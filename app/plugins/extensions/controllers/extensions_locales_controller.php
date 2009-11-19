<?php
/**
 * Extensions Locales Controller
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
class ExtensionsLocalesController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    var $name = 'ExtensionsLocales';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    var $uses = array('Setting', 'User');

    function beforeFilter() {
        parent::beforeFilter();
        App::import('Core', 'File');
        APP::import('Core', 'Folder');
    }

    function admin_index() {
        $this->pageTitle = __('Locales', true);

        $folder =& new Folder;
        $folder->path = APP . 'locale';
        $content = $folder->read();
        $locales = $content['0'];
        $this->set(compact('content', 'locales'));
    }

    function admin_activate($locale = null) {
        if ($locale == null || !is_dir(APP . 'locale' . DS . $locale)) {
            $this->Session->setFlash(__('Locale does not exist.', true));
            $this->redirect(array('action' => 'index'));
            exit();
        }

        $file =& new File(APP . 'config' . DS . 'croogo_bootstrap.php', true);
        $content = $file->read();

        $content = str_replace("Configure::write('Config.language', '" . Configure::read('Site.locale') . "');",
            "Configure::write('Config.language', '" . $locale . "');", $content);
        if ($file->write($content)) {
            $this->Setting->write('Site.locale', $locale);
            $this->Session->setFlash(__("Locale '{$locale}' set as default", true));
        } else {
            $this->Session->setFlash(__('Could not edit croogo_bootstrap.php file.', true));
        }
        $this->redirect(array('action' => 'index'));
        exit();
    }

    function admin_add() {
        $this->pageTitle = __('Upload a new locale', true);

        if (!empty($this->data)) {
            $file = $this->data['Locale']['file'];
            unset($this->data['Locale']['file']);

            // get locale name
            $zip = zip_open($file['tmp_name']);
            $locale = null;
            if ($zip) {
                while ($zip_entry = zip_read($zip)) {
                    $zipEntryName = zip_entry_name($zip_entry);
                    if (strstr($zipEntryName, 'LC_MESSAGES')) {
                        $zipEntryNameE = explode('/LC_MESSAGES', $zipEntryName);
                        if (isset($zipEntryNameE['0'])) {
                            $pathE = explode('/', $zipEntryNameE['0']);
                            if (isset($pathE[count($pathE) - 1])) {
                                $locale = $pathE[count($pathE) - 1];
                            }
                        }
                    }
                }
            }
            zip_close($zip);

            if (!$locale) {
                $this->Session->setFlash(__('Invalid locale.', true));
                $this->redirect(array('action' => 'add'));
                exit();
            }

            if (is_dir(APP . 'locale' . DS . $locale)) {
                $this->Session->setFlash(__('Locale already exists.', true));
                $this->redirect(array('action' => 'add'));
                exit();
            }

            // extract
            $zip = zip_open($file['tmp_name']);
            if ($zip) {
                while ($zip_entry = zip_read($zip)) {
                    $zipEntryName = zip_entry_name($zip_entry);
                    if (strstr($zipEntryName, $locale . '/')) {
                        $zipEntryNameE = explode($locale . '/', $zipEntryName);
                        if (isset($zipEntryNameE['1'])) {
                            $path = APP . 'locale' . DS . $locale . DS . str_replace('/', DS, $zipEntryNameE['1']);
                        } else {
                            $path = APP . 'locale' . DS . $locale . DS;
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
            exit();
        }
    }

    function admin_edit($locale = null) {
        $this->pageTitle = __('Edit locale: ' . $locale, true);

        if (!$locale) {
            $this->Session->setFlash(__('Invalid locale.', true));
            $this->redirect(array('action' => 'index'));
            exit();
        }

        if (!file_exists(APP . 'locale' . DS . $locale . DS . 'LC_MESSAGES' . DS . 'default.po')) {
            $this->Session->setFlash(__('The file default.po does not exist.', true));
            $this->redirect(array('action' => 'index'));
            exit();
        }

        $file =& new File(APP . 'locale' . DS . $locale . DS . 'LC_MESSAGES' . DS . 'default.po', true);
        $content = $file->read();

        if (!empty($this->data)) {
            // save
            if ($file->write($this->data['Locale']['content'])) {
                $this->Session->setFlash(__('Locale updated successfully', true));
                $this->redirect(array('action' => 'index'));
                exit();
            }
        }

        $this->set(compact('locale', 'content'));
    }

    function admin_delete($locale = null) {
        if (!$locale) {
            $this->Session->setFlash(__('Invalid locale', true));
            $this->redirect(array('action' => 'index'));
            exit();
        }

        $folder =& new Folder;
        if ($folder->delete(APP . 'locale' . DS . $locale)) {
            $this->Session->setFlash(__('Locale deleted successfully.', true));
        } else {
            $this->Session->setFlash(__('Local could not be deleted.', true));
        }

        $this->redirect(array('action' => 'index'));
        exit();
    }

}
?>