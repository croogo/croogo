<?php
/**
 * Themes Controller
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
class ThemesController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    var $name = 'Themes';
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
        App::import('Xml');
    }

    function admin_index() {
        $this->pageTitle = __('Themes', true);

        $themes = $this->Theme->getThemes();
        $themesData = array();
        $themesData[] = $this->Theme->getData();
        foreach ($themes AS $theme) {
            $themesData[] = $this->Theme->getData($theme);
        }

        $currentTheme = $this->Theme->getData(Configure::read('Site.theme'));
        $this->set(compact('themes', 'themesData', 'currentTheme'));
    }

    function admin_activate($alias = null) {
        if ($alias == 'default') {
            $alias = null;
        }

        $siteTheme = $this->Setting->findByKey('Site.theme');
        $siteTheme['Setting']['value'] = $alias;
        $this->Setting->save($siteTheme);
        $this->Session->setFlash(__('Theme activated.', true));

        $this->redirect(array('action' => 'index'));
    }

    function admin_add() {
        $this->pageTitle = __('Upload a new theme', true);

        if (!empty($this->data)) {
            $file = $this->data['Theme']['file'];
            unset($this->data['Theme']['file']);

            // get Theme alias
            $xmlContent = '';
            $zip = zip_open($file['tmp_name']);
            if ($zip) {
                while ($zip_entry = zip_read($zip)) {
                    $zipEntryName = zip_entry_name($zip_entry);
                    if (strstr($zipEntryName, 'theme.xml') &&
                        zip_entry_open($zip, $zip_entry, "r")) {
                        $xmlContent = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                    }
                }
                zip_close($zip);
            }
            if ($xmlContent == '') {
                $this->Session->setFlash(__('Invalid XML file', true));
                $this->redirect(array('action' => 'index'));
                exit();
            }

            // check if alias already exists
            App::import('Xml');
            $xmlData =& new XML($xmlContent);
            $xmlData = Set::reverse($xmlData);
            if (!isset($xmlData['Theme']['alias']) ||
                $xmlData['Theme']['alias'] == '') {
                $this->Session->setFlash(__('Invalid XML file', true));
                $this->redirect(array('action' => 'index'));
                exit();
            }
            $themeAlias = $xmlData['Theme']['alias'];
            if (is_dir(APP.'views'.DS.'themed'.DS.$themeAlias) ||
                is_dir(APP.'webroot'.DS.'themed'.DS.$themeAlias)) {
                $this->Session->setFlash(__('Directory with theme alias already exists.', true));
                $this->redirect(array('action' => 'add'));
                exit();
            }

            // extract it
            $zip = zip_open($file['tmp_name']);
            if ($zip) {
                while ($zip_entry = zip_read($zip)) {
                    $zipEntryName = zip_entry_name($zip_entry);
                    if (strstr($zipEntryName, '/themed/' . $themeAlias)) {
                        // views
                        if (strstr($zipEntryName, 'views/themed/' . $themeAlias)) {
                            $zipEntryNameE = explode('views/themed/' . $themeAlias, $zipEntryName);
                            $path = APP.'views'.DS.'themed'.DS.$themeAlias.str_replace('/', DS, $zipEntryNameE['1']);
                        }

                        // webroot
                        if (strstr($zipEntryName, 'webroot/themed/' . $themeAlias)) {
                            $zipEntryNameE = explode('webroot/themed/' . $themeAlias, $zipEntryName);
                            $path = APP.'webroot'.DS.'themed'.DS.$themeAlias.str_replace('/', DS, $zipEntryNameE['1']);
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
                $this->redirect(array('action' => 'index'));
                exit();
            }
        }
    }

}
?>