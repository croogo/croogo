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
    public $name = 'ExtensionsLocales';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    public $uses = array('Setting', 'User');

    public function beforeFilter() {
        parent::beforeFilter();
        App::import('Core', 'File');
        APP::import('Core', 'Folder');
    }

    public function admin_index() {
        $this->set('title_for_layout', __('Locales', true));

        $folder =& new Folder;
        $folder->path = APP . 'locale';
        $content = $folder->read();
        $locales = $content['0'];
        foreach($locales as $i => $locale) {
            if (strstr($locale, '.') !== false) {
                unset($locales[$i]);
            }
        }
        $this->set(compact('content', 'locales'));
    }

    public function admin_activate($locale = null) {
        if ($locale == null || !is_dir(APP . 'locale' . DS . $locale)) {
            $this->Session->setFlash(__('Locale does not exist.', true), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }

        $file =& new File(APP . 'config' . DS . 'croogo_bootstrap.php', true);
        $content = $file->read();

        $content = str_replace("Configure::write('Config.language', '" . Configure::read('Site.locale') . "');",
            "Configure::write('Config.language', '" . $locale . "');", $content);
        if ($file->write($content)) {
            $this->Setting->write('Site.locale', $locale);
            $this->Session->setFlash(sprintf(__("Locale '%s' set as default", true), $locale), 'default', array('class' => 'success'));
        } else {
            $this->Session->setFlash(__('Could not edit croogo_bootstrap.php file.', true), 'default', array('class' => 'error'));
        }
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->set('title_for_layout', __('Upload a new locale', true));

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
                $this->Session->setFlash(__('Invalid locale.', true), 'default', array('class' => 'error'));
                $this->redirect(array('action' => 'add'));
            }

            if (is_dir(APP . 'locale' . DS . $locale)) {
                $this->Session->setFlash(__('Locale already exists.', true), 'default', array('class' => 'error'));
                $this->redirect(array('action' => 'add'));
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
        }
    }

    public function admin_edit($locale = null) {
        $this->set('title_for_layout', sprintf(__('Edit locale: %s', true), $locale));

        if (!$locale) {
            $this->Session->setFlash(__('Invalid locale.', true), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
        }

        if (!file_exists(APP . 'locale' . DS . $locale . DS . 'LC_MESSAGES' . DS . 'default.po')) {
            $this->Session->setFlash(__('The file default.po does not exist.', true), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
        }

        $file =& new File(APP . 'locale' . DS . $locale . DS . 'LC_MESSAGES' . DS . 'default.po', true);
        $content = $file->read();

        if (!empty($this->data)) {
            // save
            if ($file->write($this->data['Locale']['content'])) {
                $this->Session->setFlash(__('Locale updated successfully', true), 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            }
        }

        $this->set(compact('locale', 'content'));
    }

    public function admin_delete($locale = null) {
        if (!$locale) {
            $this->Session->setFlash(__('Invalid locale', true), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }

        $folder =& new Folder;
        if ($folder->delete(APP . 'locale' . DS . $locale)) {
            $this->Session->setFlash(__('Locale deleted successfully.', true), 'default', array('class' => 'success'));
        } else {
            $this->Session->setFlash(__('Local could not be deleted.', true), 'default', array('class' => 'error'));
        }

        $this->redirect(array('action' => 'index'));
    }

}
?>