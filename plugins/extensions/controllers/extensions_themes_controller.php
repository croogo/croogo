<?php
/**
 * Extensions Themes Controller
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
class ExtensionsThemesController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    public $name = 'ExtensionsThemes';
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
        App::import('Core', 'Folder');
    }

    public function admin_index() {
        $this->set('title_for_layout', __('Themes', true));

        $themes = $this->Croogo->getThemes();
        $themesData = array();
        $themesData[] = $this->Croogo->getThemeData();
        foreach ($themes AS $theme) {
            $themesData[$theme] = $this->Croogo->getThemeData($theme);
        }

        $currentTheme = $this->Croogo->getThemeData(Configure::read('Site.theme'));
        $this->set(compact('themes', 'themesData', 'currentTheme'));
    }

    public function admin_activate($alias = null) {
        if ($alias == 'default') {
            $alias = null;
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }

        $siteTheme = $this->Setting->findByKey('Site.theme');
        $siteTheme['Setting']['value'] = $alias;
        $this->Setting->save($siteTheme);
        $this->Session->setFlash(__('Theme activated.', true), 'default', array('class' => 'success'));

        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->set('title_for_layout', __('Upload a new theme', true));

        if (!empty($this->data)) {
            $file = $this->data['Theme']['file'];
            unset($this->data['Theme']['file']);

            // get Theme YML and alias
            $ymlContent = '';
            $zip = zip_open($file['tmp_name']);
            if ($zip) {
                while ($zip_entry = zip_read($zip)) {
                    $zipEntryName = zip_entry_name($zip_entry);
                    if (strstr($zipEntryName, 'theme.yml') &&
                        zip_entry_open($zip, $zip_entry, "r")) {
                        $ymlContent = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

                        $zipEntryNameE = explode('/', $zipEntryName);
                        if (isset($zipEntryNameE['0'])) {
                            $themeAlias = $zipEntryNameE[count($zipEntryNameE) - 3];
                        }
                    }
                }
                zip_close($zip);
            }
            if ($ymlContent == '') {
                $this->Session->setFlash(__('Invalid YML file', true), 'default', array('class' => 'error'));
                $this->redirect(array('action' => 'index'));
            }

            // check if alias already exists
            if (!isset($themeAlias)) {
                $this->Session->setFlash(__('Invalid zip archive', true), 'default', array('class' => 'error'));
                $this->redirect(array('action' => 'index'));
            }
            if (is_dir(APP.'views'.DS.'themed'.DS.$themeAlias) ||
                is_dir(APP.'webroot'.DS.'themed'.DS.$themeAlias)) {
                $this->Session->setFlash(__('Directory with theme alias already exists.', true), 'default', array('class' => 'error'));
                $this->redirect(array('action' => 'add'));
            }

            // extract it
            $zip = zip_open($file['tmp_name']);
            if ($zip) {
                while ($zip_entry = zip_read($zip)) {
                    $zipEntryName = zip_entry_name($zip_entry);
                    if (strstr($zipEntryName, $themeAlias . '/')) {
                        $zipEntryNameE = explode($themeAlias . '/', $zipEntryName);
                        if (isset($zipEntryNameE['1'])) {
                            $path = APP . 'views' . DS . 'themed' . DS . $themeAlias . DS . str_replace('/', DS, $zipEntryNameE['1']);
                        } else {
                            $path = APP . 'views' . DS . 'themed' . DS . $themeAlias . DS;
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
                zip_close($zip);
                $this->Session->setFlash(__('Theme uploaded successfully.', true), 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            }
        }
    }

    public function admin_editor() {
        $this->set('title_for_layout', __('Theme Editor', true));
    }

    public function admin_save() {

    }

    public function admin_delete($alias = null) {
        if ($alias == null) {
            $this->Session->setFlash(__('Invalid Theme.', true), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }

        if ($alias == 'default') {
            $this->Session->setFlash(__('Default theme cannot be deleted.', true), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
        } elseif ($alias == Configure::read('Site.theme')) {
            $this->Session->setFlash(__('You cannot delete a theme that is currently active.', true), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
        }

        $paths = array(
            APP . 'webroot' . DS . 'theme' . DS . $alias . DS,
            APP . 'views' . DS . 'themed' . DS . $alias . DS,
        );

        $error = 0;
        $folder =& new Folder;
        foreach ($paths AS $path) {
            if (is_dir($path)) {
                if (!$folder->delete($path)) {
                    $error = 1;
                }
            }
        }

        if ($error == 1) {
            $this->Session->setFlash(__('An error occurred.', true), 'default', array('class' => 'error'));
        } else {
            $this->Session->setFlash(__('Theme deleted successfully.', true), 'default', array('class' => 'success'));
        }

        $this->redirect(array('action' => 'index'));
    }

}
?>