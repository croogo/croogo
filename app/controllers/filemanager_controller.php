<?php
/**
 * Filemanager Controller
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
class FilemanagerController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    var $name = 'Filemanager';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    var $uses = array('Setting', 'User');
/**
 * Helpers used by the Controller
 *
 * @var array
 * @access public
 */
    var $helpers = array('Html', 'Form', 'Filemanager');

    var $deletablePaths = array();

    function beforeFilter() {
        parent::beforeFilter();

        $this->deletablePaths = array(
            APP.'views'.DS.'themed'.DS,
            WWW_ROOT.'css'.DS,
            WWW_ROOT.'img'.DS,
            WWW_ROOT.'js'.DS,
            WWW_ROOT.'themed'.DS,
        );
        $this->set('deletablePaths', $this->deletablePaths);

        //App::import('Core', 'Folder');
        App::import('Core', 'File');
    }

    function admin_index() {
        $this->redirect(array('action' => 'browse'));
        die();
    }

    function admin_browse() {
        $this->folder = new Folder;

        if (isset($this->params['url']['path'])) {
            $path = $this->params['url']['path'];
        } else {
            $path = APP;
        }

        $this->pageTitle = "File Manager";
        $this->folder->path = $path;

        $content = $this->folder->read();
        $this->set(compact('content'));
        $this->set('path', $path);
    }

    function admin_editfile() {
        if (isset($this->params['url']['path'])) {
            $path = $this->params['url']['path'];
            $absolutefilepath = $path;
        } else {
            $this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
        }

        $this->pageTitle = "Edit File: " . $path;

        $path_e = explode(DS, $path);
        $n = count($path_e) - 1;
        $filename = $path_e[$n];
        unset($path_e[$n]);
        $path = implode(DS, $path_e);
        $this->file = new File($absolutefilepath, true);

        if( !empty($this->data) ) {
            // save
            if( $this->file->write($this->data['Filemanager']['content']) ) {
                $this->Session->setFlash(__('File saved successfully', true));
            }
        }

        $content = $this->file->read();

        $this->set(compact('content', 'path', 'absolutefilepath'));
    }

    function admin_upload() {
        $this->pageTitle = __('Upload', true);

        if (isset($this->params['url']['path'])) {
            $path = $this->params['url']['path'];
        } else {
            $path = APP;
        }

        $this->set(compact('path'));

        if (isset($this->data) &&
            is_uploaded_file($this->data['Filemanager']['file']['tmp_name'])) {
            $destination = $path.$this->data['Filemanager']['file']['name'];
            move_uploaded_file($this->data['Filemanager']['file']['tmp_name'], $destination);
            $this->Session->setFlash(__('File uploaded successfully.', true));
            $redirectUrl = Router::url(array('controller' => 'filemanager', 'action' => 'browse'), true) . '?path=' . urlencode($path);

            $this->redirect($redirectUrl);
        }
    }

    function admin_delete_file() {
        if (isset($this->params['url']['path'])) {
            $path = $this->params['url']['path'];
        } else {
            $this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
        }

        if (file_exists($path) && unlink($path)) {
            $this->Session->setFlash(__('File deleted', true));
        } else {
            $this->Session->setFlash(__('An error occured', true));
        }

        if (isset($_SERVER['HTTP_REFERER'])) {
            $this->redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->redirect(array('controller' => 'filemanager', 'action' => 'index'));
        }

        exit();
    }

    function admin_delete_directory() {
        if (isset($this->params['url']['path'])) {
            $path = $this->params['url']['path'];
        } else {
            $this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
        }

        if (is_dir($path) && rmdir($path)) {
            $this->Session->setFlash(__('Directory deleted', true));
        } else {
            $this->Session->setFlash(__('An error occured', true));
        }

        if (isset($_SERVER['HTTP_REFERER'])) {
            $this->redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->redirect(array('controller' => 'filemanager', 'action' => 'index'));
        }

        exit();
    }

    function admin_rename() {
        if (isset($this->params['url']['path'])) {
            $path = $this->params['url']['path'];
        } else {
            $this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
        }

        if (isset($this->params['url']['newpath'])) {
            // rename here
        }

        if (isset($_SERVER['HTTP_REFERER'])) {
            $this->redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->redirect(array('controller' => 'filemanager', 'action' => 'index'));
        }
    }

    function admin_create_directory() {
        $this->pageTitle = __('New Directory', true);

        if (isset($this->params['url']['path'])) {
            $path = $this->params['url']['path'];
        } else {
            $this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
        }

        if (!empty($this->data)) {
            $this->folder = new Folder;
            if ($this->folder->create($path . $this->data['Filemanager']['name'])) {
                $this->Session->setFlash(__('Directory created successfully.', true));
                $redirectUrl = Router::url(array('controller' => 'filemanager', 'action' => 'browse'), true) . '?path=' . urlencode($path);
                $this->redirect($redirectUrl);
            } else {
                $this->Session->setFlash(__('An error occured', true));
            }
        }

        $this->set(compact('path'));
    }

    function admin_create_file() {
        $this->pageTitle = __('New File', true);

        if (isset($this->params['url']['path'])) {
            $path = $this->params['url']['path'];
        } else {
            $this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
        }

        if (!empty($this->data)) {
            //$this->file = new File;
            //if ($this->file->create($path . $this->data['Filemanager']['name'])) {
            if (touch($path . $this->data['Filemanager']['name'])) {
                $this->Session->setFlash(__('File created successfully.', true));
                $redirectUrl = Router::url(array('controller' => 'filemanager', 'action' => 'browse'), true) . '?path=' . urlencode($path);
                $this->redirect($redirectUrl);
            } else {
                $this->Session->setFlash(__('An error occured', true));
            }
        }

        $this->set(compact('path'));
    }

    function admin_chmod() {

    }

}
?>