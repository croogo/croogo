<?php

namespace Croogo\FileManager\Controller\Admin;

use Cake\Core\Configure;
use Cake\Event\Event;
use Croogo\Core\Utility\FsUtils;
use Cake\Routing\Router;
use Croogo\FileManager\Utility\FileManager;

/**
 * FileManager Controller
 *
 * @category FileManager.Controller
 * @package  Croogo.FileManager.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class FileManagerController extends AppController
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->FileManager = new FileManager();
        $this->viewBuilder()
            ->setHelpers([
                'Croogo/Core.Image',
                'Croogo/FileManager.FileManager',
            ]);
    }

    /**
     * Helper to generate a browse url for $path
     *
     * @param string $path Path
     * @return string
     */
    protected function _browsePathUrl($path)
    {
        return Router::url([
            'controller' => 'FileManager',
            'action' => 'browse',
            '?' => [
                'path' => $path,
            ],
        ], true);
    }

    /**
     * Admin index
     *
     * @return Cake\Http\Response|void
     * @access public
     */
    public function index()
    {
        return $this->redirect(['action' => 'browse']);
    }

    /**
     * Admin browse
     *
     * @return void
     * @access public
     */
    public function browse(): void
    {
        $path = (string)($this->getRequest()->getQuery('path') ?: WWW_ROOT);

        $realPath = realpath($path);
        $root = realpath(ROOT);
        if ($realPath === false || ($realPath !== $root && !str_starts_with($realPath, $root . DS))) {
            $this->Flash->error(__d('croogo', 'Path %s is restricted', $path));
            $realPath = rtrim(APP, DS);
        }
        $path = $realPath . DS;

        // Matched per path segment: a substring match would also hide e.g. `.github`.
        $blacklist = ['.git', '.svn', 'CVS'];
        $quoted = array_map(fn($dir) => preg_quote($dir, '#'), $blacklist);
        $regex = '#(?<=^|[\\\\/])(' . implode('|', $quoted) . ')(?=[\\\\/]|$)#';
        if (preg_match($regex, $path, $matches, PREG_OFFSET_CAPTURE)) {
            $this->Flash->error(__d('croogo', 'Path %s is restricted', $path));
            $path = substr($path, 0, $matches[1][1]);
        }

        $content = FsUtils::read($path);
        $this->set(compact('content'));
        $this->set('path', $path);
    }

    /**
     * Admin edit file
     *
     * @return Cake\Http\Response|void
     * @access public
     */
    public function editFile()
    {
        if (!empty($this->getRequest()->getQuery('path'))) {
            $path = $this->getRequest()->getQuery('path');
            $absolutefilepath = $path;
        } else {
            return $this->redirect(['controller' => 'FileManager', 'action' => 'browse']);
        }
        if (!$this->FileManager->isEditable($path) || !is_file($path) || !$this->FileManager->isAllowedName(basename($path))) {
            $this->Flash->error(__d('croogo', 'Path %s is restricted', $path));

            return $this->redirect(['controller' => 'FileManager', 'action' => 'browse']);
        }

        $pathE = explode(DS, $path);
        $n = count($pathE) - 1;
        unset($pathE[$n]);
        $path = implode(DS, $pathE);

        // Cake 5: File usunięty — natywne file_put_contents/file_get_contents.
        if (!empty($this->getRequest()->getData())) {
            if (file_put_contents($absolutefilepath, $this->getRequest()->getData('content')) !== false) {
                $this->Flash->success(__d('croogo', 'File saved successfully'));
            }
        }

        $content = (string)file_get_contents($absolutefilepath);

        $this->set(compact('content', 'path', 'absolutefilepath'));
    }

    /**
     * Admin upload
     *
     * @return Cake\Http\Response|void
     * @access public
     */
    public function upload()
    {
        $this->set('title_for_layout', __d('croogo', 'Upload'));

        $path = $this->getRequest()->getQuery('path') ?: APP;
        $this->set(compact('path'));

        if (!$this->FileManager->isWritableDirectory($path)) {
            $this->Flash->error(__d('croogo', 'Path %s is restricted', $path));

            return $this->redirect($this->referer());
        }

        // Cake 5: upload to obiekt UploadedFileInterface, nie tablica tmp_name
        $postFile = $this->getRequest()->getData('file');
        if ($postFile instanceof \Psr\Http\Message\UploadedFileInterface &&
            $postFile->getError() === UPLOAD_ERR_OK
        ) {
            $destination = $this->FileManager->childPath($path, $postFile->getClientFilename());
            if ($destination === null) {
                $this->Flash->error(__d('croogo', 'Path %s is restricted', $path));

                return $this->redirect($this->referer());
            }
            $postFile->moveTo($destination);
            $this->Flash->success(__d('croogo', 'File uploaded successfully.'));
            $redirectUrl = $this->_browsePathUrl($path);

            return $this->redirect($redirectUrl);
        }
    }

    /**
     * Admin Delete File
     *
     * @return Cake\Http\Response|void
     * @access public
     */
    public function deleteFile()
    {
        // Cake 5: legacy property $request->data nie istnieje -> getData()
        if (!empty($this->getRequest()->getData('path'))) {
            $path = $this->getRequest()->getData('path');
        } else {
            return $this->redirect(['controller' => 'FileManager', 'action' => 'browse']);
        }

        if (!$this->FileManager->isDeletable($path) || !is_file($path)) {
            $this->Flash->error(__d('croogo', 'Path %s is restricted', $path));

            return $this->redirect(['controller' => 'FileManager', 'action' => 'browse']);
        }

        if (unlink($path)) {
            $this->Flash->success(__d('croogo', 'File deleted'));
        } else {
            $this->Flash->error(__d('croogo', 'An error occured'));
        }

        if (isset($_SERVER['HTTP_REFERER'])) {
            return $this->redirect($_SERVER['HTTP_REFERER']);
        } else {
            return $this->redirect(['controller' => 'FileManager', 'action' => 'index']);
        }

        exit();
    }

    /**
     * Admin Delete Directory
     *
     * @return Cake\Http\Response|void
     * @access public
     */
    public function deleteDirectory()
    {
        // Cake 5: legacy property $request->data nie istnieje -> getData()
        if (!empty($this->getRequest()->getData('path'))) {
            $path = $this->getRequest()->getData('path');
        } else {
            return $this->redirect(['controller' => 'FileManager', 'action' => 'browse']);
        }

        if (!$this->FileManager->isDeletable($path) || !is_dir($path)) {
            $this->Flash->error(__d('croogo', 'Path %s is restricted', $path));

            return $this->redirect(['controller' => 'FileManager', 'action' => 'browse']);
        }

        if (FsUtils::deleteTree($path)) {
            $this->Flash->success(__d('croogo', 'Directory deleted'));
        } else {
            $this->Flash->error(__d('croogo', 'An error occured'));
        }

        if (isset($_SERVER['HTTP_REFERER'])) {
            return $this->redirect($_SERVER['HTTP_REFERER']);
        } else {
            return $this->redirect(['controller' => 'FileManager', 'action' => 'index']);
        }

        exit;
    }

    /**
     * Rename a file or directory
     *
     * @return Cake\Http\Response|void
     * @access public
     */
    public function rename()
    {
        // PHP 8.1+: null do explode() deprecated (brak ?path= w URL)
        $path = (string)$this->getRequest()->getQuery('path');
        $pathFragments = array_filter(explode(DIRECTORY_SEPARATOR, $path));

        // The parent must be editable too, so a configured root itself is never renamed.
        if (!$this->FileManager->isEditable($path) || !$this->FileManager->isEditable(dirname($path))) {
            $this->Flash->error(__d('croogo', 'Path "%s" cannot be renamed', $path));

            return $this->redirect(['controller' => 'FileManager', 'action' => 'browse']);
        }

        if ($this->getRequest()->is('post') || $this->getRequest()->is('put')) {
            // Cake 5: legacy property $request->data nie istnieje -> getData()
            if (!is_null($this->getRequest()->getData('name')) &&
                !empty($this->getRequest()->getData('name'))
            ) {
                $newName = trim($this->getRequest()->getData('name'));
                $oldName = array_pop($pathFragments);
                // dirname() zamiast DS.implode(DS,...) - tamto psulo sciezki
                // Windows (wiodacy \ przed litera dysku, np. \C:\...)
                $newPath = $this->FileManager->childPath(dirname($path), $newName);

                $fileExists = $newPath !== null && file_exists($newPath);
                if ($newPath === null || !$this->FileManager->isEditable(dirname($newPath))) {
                    $message = __d('croogo', 'Path "%s" cannot be renamed', $path);
                    $alertType = 'error';
                } elseif ($oldName !== $newName) {
                    if ($fileExists) {
                        $message = __d('croogo', '%s already exists', $newName);
                        $alertType = 'error';
                    } else {
                        if ($this->FileManager->rename($path, $newPath)) {
                            $message = __d('croogo', '"%s" has been renamed to "%s"', $oldName, $newName);
                            $alertType = 'success';
                        } else {
                            $message = __d('croogo', 'Could not rename "%s" to "%s"', $oldName, $newName);
                            $alertType = 'error';
                        }
                    }
                } else {
                    $message = __d('croogo', 'Name has not changed');
                    $alertType = 'warning';
                }
                $this->Flash->{$alertType}($message);
            }

            $redirectUrl = ['controller' => 'FileManager', 'action' => 'browse'];

            return $this->redirect($redirectUrl);
        }
        // Cake 4: request immutable -> withData() + setRequest() (prefill pola 'name').
        $this->setRequest($this->getRequest()->withData('name', array_pop($pathFragments)));
        $this->set('path', $path);
    }

    /**
     * Admin Create Directory
     *
     * @return Cake\Http\Response|void
     * @access public
     */
    public function createDirectory()
    {
        // Cake 5: legacy property $request->query nie istnieje -> getQuery()
        if ($this->getRequest()->getQuery('path') !== null) {
            $path = $this->getRequest()->getQuery('path');
        } else {
            return $this->redirect(['controller' => 'FileManager', 'action' => 'browse']);
        }

        if (!$this->FileManager->isWritableDirectory($path)) {
            $this->Flash->error(__d('croogo', 'Path %s is restricted', $path));

            return $this->redirect($this->referer());
        }

        if (!empty($this->getRequest()->getData())) {
            $target = $this->FileManager->childPath($path, $this->getRequest()->getData('name'));
            if ($target !== null && mkdir($target, 0777, true)) {
                $this->Flash->success(__d('croogo', 'Directory created successfully.'));
                $redirectUrl = $this->_browsePathUrl($path);

                return $this->redirect($redirectUrl);
            } else {
                $this->Flash->error(__d('croogo', 'An error occured'));
            }
        }

        $this->set(compact('path'));
    }

    /**
     * Admin Create File
     *
     * @return Cake\Http\Response|void
     * @access public
     */
    public function createFile()
    {
        // Cake 5: legacy property $request->query nie istnieje -> getQuery()
        if ($this->getRequest()->getQuery('path') !== null) {
            $path = $this->getRequest()->getQuery('path');
        } else {
            return $this->redirect(['controller' => 'FileManager', 'action' => 'browse']);
        }

        if (!$this->FileManager->isEditable($path) || !is_dir($path)) {
            $this->Flash->error(__d('croogo', 'Path %s is restricted', $path));

            return $this->redirect($this->referer());
        }

        if (!empty($this->getRequest()->getData())) {
            $target = $this->FileManager->childPath($path, $this->getRequest()->getData('name'));
            if ($target !== null && file_put_contents($target, $this->getRequest()->getData('content'))) {
                $this->Flash->success(__d('croogo', 'File created successfully.'));
                $redirectUrl = $this->_browsePathUrl($path);

                return $this->redirect($redirectUrl);
            } else {
                $this->Flash->error(__d('croogo', 'An error occured'));
            }
        }

        $this->set(compact('path'));
    }

    /**
     * Admin chmod
     *
     * @return void
     * @access public
     */
    public function chmod(): void
    {
    }
}
