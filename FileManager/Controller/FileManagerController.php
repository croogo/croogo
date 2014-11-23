<?php

App::uses('FileManagerAppController', 'FileManager.Controller');
App::uses('FileManager', 'FileManager.Model');
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');

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
class FileManagerController extends FileManagerAppController {

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array('Settings.Setting', 'Users.User', 'FileManager.FileManager');

/**
 * Helpers used by the Controller
 *
 * @var array
 * @access public
 */
	public $helpers = array('Html', 'Form', 'FileManager.FileManager');

/**
 * Deletable Paths
 *
 * @var array
 * @access public
 */
	public $deletablePaths = array();

/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->deletablePaths = array(
			APP . 'View' . DS . 'Themed' . DS,
			WWW_ROOT,
		);
		$this->set('deletablePaths', $this->deletablePaths);
	}

/**
 * Checks wether given $path is editable.
 * A file is editable when it resides under the APP directory
 *
 * @param string $path Path to check
 * @return boolean true if file is editable
 * @deprecated Use FileManager::isEditable()
 */
	protected function _isEditable($path) {
		$path = realpath($path);
		$regex = '/^' . preg_quote(realpath(APP), '/') . '/';
		return preg_match($regex, $path) > 0;
	}

/**
 * Checks wether given $path is editable.
 * A file is deleteable when it resides under directories registered in
 * FileManagerController::deletablePaths
 *
 * @param string $path Path to check
 * @return boolean true when file is deletable
 * @deprecated Use FileManager::isDeletable()
 */
	protected function _isDeletable($path) {
		$path = realpath($path);
		$regex = array();
		for ($i = 0, $ii = count($this->deletablePaths); $i < $ii; $i++) {
			$regex[] = '(^' . preg_quote(realpath($this->deletablePaths[$i]), '/') . ')';
		}
		$regex = '/' . join($regex, '|') . '/';
		return preg_match($regex, $path) > 0;
	}

/**
 * Admin index
 *
 * @return void
 * @access public
 */
	public function admin_index() {
		return $this->redirect(array('action' => 'browse'));
	}

/**
 * Admin browse
 *
 * @return void
 * @access public
 */
	public function admin_browse() {
		$this->folder = new Folder;

		if (isset($this->request->query['path'])) {
			$path = $this->request->query['path'];
		} else {
			$path = APP;
		}

		$this->set('title_for_layout', __d('croogo', 'File Manager'));

		$path = realpath($path) . DS;
		$regex = '/^' . preg_quote(realpath(APP), '/') . '/';
		if (preg_match($regex, $path) == false) {
			$this->Session->setFlash(__d('croogo', 'Path %s is restricted', $path), 'flash', array('class' => 'error'));
			$path = APP;
		}

		$blacklist = array('.git', '.svn', '.CVS');
		$regex = '/(' . preg_quote(implode('|', $blacklist), '.') . ')/';
		if (in_array(basename($path), $blacklist) || preg_match($regex, $path)) {
			$this->Session->setFlash(__d('croogo', sprintf('Path %s is restricted', $path)), 'flash', array('class' => 'error'));
			$path = dirname($path);
		}

		$this->folder->path = $path;

		$content = $this->folder->read();
		$this->set(compact('content'));
		$this->set('path', $path);
	}

/**
 * Admin editfile
 *
 * @return void
 * @access public
 */
	public function admin_editfile() {
		if (isset($this->request->query['path'])) {
			$path = $this->request->query['path'];
			$absolutefilepath = $path;
		} else {
			return $this->redirect(array('controller' => 'file_manager', 'action' => 'browse'));
		}
		if (!$this->FileManager->isEditable($path)) {
			$this->Session->setFlash(__d('croogo', 'Path %s is restricted', $path), 'flash', array('class' => 'error'));
			return $this->redirect(array('controller' => 'file_manager', 'action' => 'browse'));
		}
		$this->set('title_for_layout', __d('croogo', 'Edit file: %s', $path));

		$pathE = explode(DS, $path);
		$n = count($pathE) - 1;
		unset($pathE[$n]);
		$path = implode(DS, $pathE);
		$this->file = new File($absolutefilepath, true);

		if (!empty($this->request->data) ) {
			if ($this->file->write($this->request->data['FileManager']['content'])) {
				$this->Session->setFlash(__d('croogo', 'File saved successfully'), 'flash', array('class' => 'success'));
			}
		}

		$content = $this->file->read();

		$this->set(compact('content', 'path', 'absolutefilepath'));
	}

/**
 * Admin upload
 *
 * @return void
 * @access public
 */
	public function admin_upload() {
		$this->set('title_for_layout', __d('croogo', 'Upload'));

		if (isset($this->request->query['path'])) {
			$path = $this->request->query['path'];
		} else {
			$path = APP;
		}
		$this->set(compact('path'));

		if (isset($path) && !$this->_isDeletable($path)) {
			$this->Session->setFlash(__d('croogo', 'Path %s is restricted', $path), 'flash', array('class' => 'error'));
			return $this->redirect($this->referer());
		}

		if (isset($this->request->data['FileManager']['file']['tmp_name']) &&
			is_uploaded_file($this->request->data['FileManager']['file']['tmp_name'])) {
			$destination = $path . $this->request->data['FileManager']['file']['name'];
			move_uploaded_file($this->request->data['FileManager']['file']['tmp_name'], $destination);
			$this->Session->setFlash(__d('croogo', 'File uploaded successfully.'), 'flash', array('class' => 'success'));
			$redirectUrl = Router::url(array('controller' => 'file_manager', 'action' => 'browse'), true) . '?path=' . urlencode($path);

			return $this->redirect($redirectUrl);
		}
	}

/**
 * Admin Delete File
 *
 * @return void
 * @access public
 */
	public function admin_delete_file() {
		if (!empty($this->request->data['path'])) {
			$path = $this->request->data['path'];
		} else {
			return $this->redirect(array('controller' => 'file_manager', 'action' => 'browse'));
		}

		if (!$this->_isDeletable($path)) {
			$this->Session->setFlash(__d('croogo', 'Path %s is restricted', $path), 'flash', array('class' => 'error'));
			return $this->redirect(array('controller' => 'file_manager', 'action' => 'browse'));
		}

		if (file_exists($path) && unlink($path)) {
			$this->Session->setFlash(__d('croogo', 'File deleted'), 'flash', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'An error occured'), 'flash', array('class' => 'error'));
		}

		if (isset($_SERVER['HTTP_REFERER'])) {
			return $this->redirect($_SERVER['HTTP_REFERER']);
		} else {
			return $this->redirect(array('controller' => 'file_manager', 'action' => 'index'));
		}

		exit();
	}

/**
 * Admin Delete Directory
 *
 * @return void
 * @access public
 */
	public function admin_delete_directory() {
		if (!empty($this->request->data['path'])) {
			$path = $this->request->data['path'];
		} else {
			return $this->redirect(array('controller' => 'file_manager', 'action' => 'browse'));
		}

		if (isset($path) && !$this->_isDeletable($path)) {
			$this->Session->setFlash(__d('croogo', 'Path %s is restricted', $path), 'flash', array('class' => 'error'));
			return $this->redirect(array('controller' => 'file_manager', 'action' => 'browse'));
		}

		if (is_dir($path) && rmdir($path)) {
			$this->Session->setFlash(__d('croogo', 'Directory deleted'), 'flash', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'An error occured'), 'flash', array('class' => 'error'));
		}

		if (isset($_SERVER['HTTP_REFERER'])) {
			return $this->redirect($_SERVER['HTTP_REFERER']);
		} else {
			return $this->redirect(array('controller' => 'file_manager', 'action' => 'index'));
		}

		exit;
	}

/**
 * Rename a file or directory
 *
 * @return void
 * @access public
 */
	public function admin_rename() {
		$path = $this->request->query('path');
		$pathFragments = array_filter(explode(DIRECTORY_SEPARATOR, $path));

		if (!$this->FileManager->isEditable($path)) {
			$this->Session->setFlash(__d('croogo', 'Path "%s" cannot be renamed', $path), 'flash', array('class' => 'error'));
			$this->redirect(array('controller' => 'file_manager', 'action' => 'browse'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			if (!is_null($this->request->data('FileManager.name')) && !empty($this->request->data['FileManager']['name'])) {
				$newName = trim($this->request->data['FileManager']['name']);
				$oldName = array_pop($pathFragments);
				$newPath = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pathFragments) . DIRECTORY_SEPARATOR . $newName;

				$fileExists = file_exists($newPath);
				if ($oldName !== $newName) {
					if ($fileExists) {
						$message = __d('croogo', '%s already exists', $newName);
						$alertType = 'error';
					} else {
						if ($this->FileManager->rename($path, $newPath)) {
							$message = __d('croogo', '"%s" has been renamed to "%s"', $oldName, $newName);
							$alertType = 'success';
						} else {
							$message = __d('croogo', 'Could not renamed "%s" to "%s"', $oldName,$newName);
							$alertType = 'error';
						}
					}
				} else {
					$message = __d('croogo', 'Name has not changed');
					$alertType = 'alert';
				}
				$this->Session->setFlash($message, 'flash', array('class' => $alertType));
			}

			return $this->Croogo->redirect(array('controller' => 'file_manager', 'action' => 'browse'));
		} else {
			$this->Croogo->setReferer();
		}
		$this->request->data('FileManager.name', array_pop($pathFragments));
		$this->set('path', $path);
	}

/**
 * Admin Create Directory
 *
 * @return void
 * @access public
 */
	public function admin_create_directory() {
		$this->set('title_for_layout', __d('croogo', 'Create Directory'));

		if (isset($this->request->query['path'])) {
			$path = $this->request->query['path'];
		} else {
			return $this->redirect(array('controller' => 'file_manager', 'action' => 'browse'));
		}

		if (isset($path) && !$this->_isDeletable($path)) {
			$this->Session->setFlash(__d('croogo', 'Path %s is restricted', $path), 'flash', array('class' => 'error'));
			return $this->redirect($this->referer());
		}

		if (!empty($this->request->data)) {
			$this->folder = new Folder;
			if ($this->folder->create($path . $this->request->data['FileManager']['name'])) {
				$this->Session->setFlash(__d('croogo', 'Directory created successfully.'), 'flash', array('class' => 'success'));
				$redirectUrl = Router::url(array('controller' => 'file_manager', 'action' => 'browse'), true) . '?path=' . urlencode($path);
				return $this->redirect($redirectUrl);
			} else {
				$this->Session->setFlash(__d('croogo', 'An error occured'), 'flash', array('class' => 'error'));
			}
		}

		$this->set(compact('path'));
	}

/**
 * Admin Create File
 *
 * @return void
 * @access public
 */
	public function admin_create_file() {
		$this->set('title_for_layout', __d('croogo', 'Create File'));

		if (isset($this->request->query['path'])) {
			$path = $this->request->query['path'];
		} else {
			return $this->redirect(array('controller' => 'file_manager', 'action' => 'browse'));
		}

		if (isset($path) && !$this->_isEditable($path)) {
			$this->Session->setFlash(__d('croogo', 'Path %s is restricted', $path), 'flash', array('class' => 'error'));
			return $this->redirect($this->referer());
		}

		if (!empty($this->request->data)) {
			if (touch($path . $this->request->data['FileManager']['name'])) {
				$this->Session->setFlash(__d('croogo', 'File created successfully.'), 'flash', array('class' => 'success'));
				$redirectUrl = Router::url(array('controller' => 'file_manager', 'action' => 'browse'), true) . '?path=' . urlencode($path);
				return $this->redirect($redirectUrl);
			} else {
				$this->Session->setFlash(__d('croogo', 'An error occured'), 'flash', array('class' => 'error'));
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
	public function admin_chmod() {
	}

}
