<?php
declare(strict_types=1);

namespace Croogo\FileManager\Controller\Admin;

use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
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
class FileManagerController extends AppController
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->fileManager = new FileManager();
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
    public function browse()
    {
        $this->folder = new Folder;

        $path = $this->getRequest()->getQuery('path') ?: WWW_ROOT;

        $path = realpath($path) . DS;
        $regex = '/^' . preg_quote(realpath(ROOT), '/') . '/';
        if (preg_match($regex, $path) == false) {
            $this->Flash->error(__d('croogo', 'Path %s is restricted', $path));
            $path = APP;
        }

        $blacklist = ['.git', '.svn', '.CVS'];
        $regex = '/(' . preg_quote(implode('|', $blacklist), '.') . ')/';
        if (in_array(basename($path), $blacklist) || preg_match($regex, $path)) {
            $this->Flash->error(__d('croogo', sprintf('Path %s is restricted', $path)));
            $path = dirname($path);
        }

        $this->folder->path = $path;

        $content = $this->folder->read();
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
        if (!$this->fileManager->isEditable($path)) {
            $this->Flash->error(__d('croogo', 'Path %s is restricted', $path));

            return $this->redirect(['controller' => 'FileManager', 'action' => 'browse']);
        }

        $pathE = explode(DS, $path);
        $n = count($pathE) - 1;
        unset($pathE[$n]);
        $path = implode(DS, $pathE);
        $this->file = new File($absolutefilepath, true);

        if (!empty($this->getRequest()->getData())) {
            if ($this->file->write($this->getRequest()->getData('content'))) {
                $this->Flash->success(__d('croogo', 'File saved successfully'));
            }
        }

        $content = $this->file->read();

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

        if (isset($path) && !$this->fileManager->isDeletable($path)) {
            $this->Flash->error(__d('croogo', 'Path %s is restricted', $path));

            return $this->redirect($this->referer());
        }

        $postFile = $this->getRequest()->getData('file');
        if (isset($postFile['tmp_name']) &&
            is_uploaded_file($postFile['tmp_name'])
        ) {
            $destination = $path . $postFile['name'];
            move_uploaded_file($postFile['tmp_name'], $destination);
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
        if (!empty($this->getRequest()->getData('path'))) {
            $path = $this->getRequest()->getData('path');
        } else {
            return $this->redirect(['controller' => 'FileManager', 'action' => 'browse']);
        }

        if (!$this->fileManager->isDeletable($path)) {
            $this->Flash->error(__d('croogo', 'Path %s is restricted', $path));

            return $this->redirect(['controller' => 'FileManager', 'action' => 'browse']);
        }

        if (file_exists($path) && unlink($path)) {
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
        if (!empty($this->getRequest()->getData('path'))) {
            $path = $this->getRequest()->getData('path');
        } else {
            return $this->redirect(['controller' => 'FileManager', 'action' => 'browse']);
        }

        if (isset($path) && !$this->fileManager->isDeletable($path)) {
            $this->Flash->error(__d('croogo', 'Path %s is restricted', $path));

            return $this->redirect(['controller' => 'FileManager', 'action' => 'browse']);
        }

        $folder = new Folder();
        if (is_dir($path) && $folder->delete($path)) {
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
        $path = $this->getRequest()->query('path');
        $pathFragments = array_filter(explode(DIRECTORY_SEPARATOR, $path));

        if (!$this->fileManager->isEditable($path)) {
            $this->Flash->error(__d('croogo', 'Path "%s" cannot be renamed', $path));

            return $this->redirect(['controller' => 'FileManager', 'action' => 'browse']);
        }

        if ($this->getRequest()->is('post') || $this->getRequest()->is('put')) {
            if (!is_null($this->getRequest()->getData('name')) &&
                !empty($this->getRequest()->getData('name'))
            ) {
                $newName = trim($this->getRequest()->getData('name'));
                $oldName = array_pop($pathFragments);
                $newPath = DIRECTORY_SEPARATOR .
                    implode(DIRECTORY_SEPARATOR, $pathFragments) .
                    DIRECTORY_SEPARATOR .
                    $newName;

                $fileExists = file_exists($newPath);
                if ($oldName !== $newName) {
                    if ($fileExists) {
                        $message = __d('croogo', '%s already exists', $newName);
                        $alertType = 'error';
                    } else {
                        if ($this->fileManager->rename($path, $newPath)) {
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
        $this->getRequest()->getData('name', array_pop($pathFragments));
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
        if (isset($this->getRequest()->query['path'])) {
            $path = $this->getRequest()->query['path'];
        } else {
            return $this->redirect(['controller' => 'FileManager', 'action' => 'browse']);
        }

        if (isset($path) && !$this->fileManager->isDeletable($path)) {
            $this->Flash->error(__d('croogo', 'Path %s is restricted', $path));

            return $this->redirect($this->referer());
        }

        if (!empty($this->getRequest()->getData())) {
            $this->folder = new Folder;
            if ($this->folder->create($path . $this->getRequest()->getData('name'))) {
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
        if (isset($this->getRequest()->query['path'])) {
            $path = $this->getRequest()->query['path'];
        } else {
            return $this->redirect(['controller' => 'FileManager', 'action' => 'browse']);
        }

        if (isset($path) && !$this->fileManager->isEditable($path)) {
            $this->Flash->error(__d('croogo', 'Path %s is restricted', $path));

            return $this->redirect($this->referer());
        }

        if (!empty($this->getRequest()->getData())) {
            if (file_put_contents($path . $this->getRequest()->getData('name'), $this->getRequest()->getData('content'))) {
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
    public function chmod()
    {
    }
}
