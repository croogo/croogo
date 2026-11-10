<?php

namespace Croogo\Extensions\Controller\Admin;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Croogo\Core\Utility\FsUtils;
use Cake\I18n\I18n;
use Locale;

/**
 * Extensions Locales Controller
 *
 * @category Controller
 * @package  Croogo.Extensions.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class LocalesController extends AppController
{

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    public $uses = [
        'Croogo/Settings.Settings',
        'Croogo/Users.Users',
    ];

    /**
     * Admin index
     *
     * @return void
     */
    public function index(): void
    {
        $this->set('title_for_layout', __d('croogo', 'Locales'));

        $locales = [];
        // Cake 5: App::path('Locale') usuniete - sciezki locales sa w konfiguracji
        $paths = (array)Configure::read('App.paths.locales');
        $currentLocale = I18n::getLocale();
        foreach ($paths as $path) {
            $content = FsUtils::read($path);
            foreach ($content[0] as $locale) {
                if (strstr($locale, '.') !== false) {
                    continue;
                }
                $fullpath = $path . $locale . DS . 'croogo.po';
                if (!file_exists($fullpath)) {
                    continue;
                }

                I18n::setLocale($locale);
                $name = Locale::getDisplayLanguage($locale);
                I18n::setLocale($currentLocale);
                $locales[$locale] = [
                    'path' => $fullpath,
                    'name' => $name,
                ];
            }
        }

        $this->set(compact('locales'));
    }

    /**
     * Admin activate
     *
     * @param string $locale
     * @return \Cake\Http\Response|void
     */
    public function activate($locale = null)
    {
        $poFile = $this->__getPoFile($locale);
        if ($locale == null || !$poFile) {
            $this->Flash->error(__d('croogo', 'Locale does not exist.'));

            return $this->redirect(['action' => 'index']);
        }

        $result = $this->Settings->write('Site.locale', $locale);
        if ($result) {
            Cache::clear('_cake_translations_');
            Cache::clear('croogo_menus');
            $this->Flash->success(__d('croogo', "Locale '%s' set as default", $locale));
        } else {
            $this->Flash->error(__d('croogo', 'Could not save Locale setting.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Deactivate locale
     *
     * @param string $locale
     * @return \Cake\Http\Response|void
     */
    public function deactivate($locale = null)
    {
        if ($locale == null) {
            $this->Flash->error(__d('croogo', 'Invalid locale.'));

            return $this->redirect(['action' => 'index']);
        }
        $result = $this->Settings->write('Site.locale', '');
        if ($result) {
            Cache::clear('_cake_translations_');
            Cache::clear('croogo_menus');
            $this->Flash->success(__d('croogo', "Locale '%s' deactivated", $locale));
        } else {
            $this->Flash->error(__d('croogo', 'Could not save Locale setting.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Admin add
     *
     * @return \Cake\Http\Response|void
     */
    public function add()
    {
        $this->set('title_for_layout', __d('croogo', 'Upload a new locale'));

        if ($this->getRequest()->is('post') && !empty($this->getRequest()->getData())) {
            // Cake 5: upload to obiekt UploadedFileInterface; PHP 8: zip_* usuniete -> ZipArchive
            $file = $this->getRequest()->getData('Locale.file');
            if (!($file instanceof \Psr\Http\Message\UploadedFileInterface) ||
                $file->getError() !== UPLOAD_ERR_OK
            ) {
                $this->Flash->error(__d('croogo', 'Invalid locale.'));

                return $this->redirect(['action' => 'add']);
            }
            $tmpName = $file->getStream()->getMetadata('uri');

            // get locale name
            $zip = new \ZipArchive();
            $locale = null;
            if ($zip->open($tmpName) === true) {
                for ($idx = 0; $idx < $zip->numFiles; $idx++) {
                    $zipEntryName = (string)$zip->getNameIndex($idx);
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
                $zip->close();
            }

            if (!$locale) {
                $this->Flash->error(__d('croogo', 'Invalid locale.'));

                return $this->redirect(['action' => 'add']);
            }

            if (is_dir(APP . 'Locale' . DS . $locale)) {
                $this->Flash->error(__d('croogo', 'Locale already exists.'));

                return $this->redirect(['action' => 'add']);
            }

            // extract (PHP 8: zip_* usuniete -> ZipArchive)
            $zip = new \ZipArchive();
            if ($zip->open($tmpName) === true) {
                for ($idx = 0; $idx < $zip->numFiles; $idx++) {
                    $zipEntryName = (string)$zip->getNameIndex($idx);
                    if (strstr($zipEntryName, $locale . '/')) {
                        $zipEntryNameE = explode($locale . '/', $zipEntryName);
                        if (isset($zipEntryNameE['1'])) {
                            $path = APP . 'Locale' . DS . $locale . DS . str_replace('/', DS, $zipEntryNameE['1']);
                        } else {
                            $path = APP . 'Locale' . DS . $locale . DS;
                        }

                        if (substr($path, strlen($path) - 1) == DS) {
                            // create directory
                            mkdir($path, 0777, true);
                        } else {
                            // create file
                            $fileContent = $zip->getFromIndex($idx);
                            if ($fileContent !== false) {
                                if (!is_dir(dirname($path))) {
                                    mkdir(dirname($path), 0777, true);
                                }
                                file_put_contents($path, $fileContent);
                            }
                        }
                    }
                }
                $zip->close();
            }

            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Admin edit
     *
     * @param string $locale
     * @return \Cake\Http\Response|void
     */
    public function edit($locale = null)
    {
        $this->set('title_for_layout', sprintf(__d('croogo', 'Edit locale: %s'), $locale));

        if (!$locale) {
            $this->Flash->error(__d('croogo', 'Invalid locale.'));

            return $this->redirect(['action' => 'index']);
        }

        $poFile = $this->__getPoFile($locale);

        if (!$poFile) {
            $this->Flash->error(__d('croogo', 'The file %s does not exist.', 'croogo.po'));

            return $this->redirect(['action' => 'index']);
        }

        // Cake 5: File usunięty — natywne operacje plikowe.
        if (!file_exists($poFile)) {
            touch($poFile);
        }
        $content = (string)file_get_contents($poFile);

        $locale = [
            'locale' => $locale,
            'content' => $content,
            'schema' => true,
        ];

        if (!empty($this->getRequest()->getData())) {
            // save
            if (file_put_contents($poFile, $this->getRequest()->getData('content')) !== false) {
                $this->Flash->success(__d('croogo', 'Locale updated successfully'));

                return $this->redirect(['action' => 'index']);
            }
        }

        $this->set(compact('locale', 'content'));
    }

    /**
     * Admin delete
     *
     * @param string $locale
     * @return \Cake\Http\Response|void
     */
    public function delete($locale = null)
    {
        $poFile = $this->__getPoFile($locale);

        if (!$poFile) {
            $this->Flash->error(__d('croogo', 'The file %s does not exist.', 'croogo.po'));

            return $this->redirect(['action' => 'index']);
        }

        if (file_exists($poFile) && unlink($poFile)) {
            $this->Flash->success(__d('croogo', 'Locale deleted successfully.'));
        } else {
            $this->Flash->error(__d('croogo', 'Local could not be deleted.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Returns the path to the croogo.po file
     *
     * @param $locale
     */
    private function __getPoFile($locale)
    {
        $paths = (array)Configure::read('App.paths.locales');
        foreach ($paths as $path) {
            $poFile = $path . $locale . DS . 'croogo.po';

            if (file_exists($poFile)) {
                return $poFile;
            }
        }

        return false;
    }
}
