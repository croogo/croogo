<?php

App::uses('File', 'Utility');

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
	public $name = 'Filemanager';

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array('Setting', 'User');

/**
 * Helpers used by the Controller
 *
 * @var array
 * @access public
 */
	public $helpers = array('Html', 'Form', 'Filemanager');

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
 * @param $path string
 * @return boolean true if file is editable
 */
	protected function _isEditable($path) {
		$path = realpath($path);
		$regex = '/^' . preg_quote(realpath(APP), '/') . '/';
		return preg_match($regex, $path) > 0;
	}

/**
 * Checks wether given $path is editable.
 * A file is deleteable when it resides under directories registered in
 * FilemanagerController::deletablePaths
 *
 * @param $path string
 * @return boolean true when file is deletable
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
		$this->redirect(array('action' => 'browse'));
		die();
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

		$this->set('title_for_layout', __('File Manager'));

		$path = realpath($path) . DS;
		$regex = '/^' . preg_quote(realpath(APP), '/') . '/';
		if (preg_match($regex, $path) == false) {
			$this->Session->setFlash(__(sprintf('Path %s is restricted', $path)));
			$path = APP;
		}

		$blacklist = array('.git', '.svn', '.CVS');
		$regex = '/(' . preg_quote(implode('|', $blacklist), '.') . ')/';
		if (in_array(basename($path), $blacklist) || preg_match($regex, $path)) {
			$this->Session->setFlash(__(sprintf('Path %s is restricted', $path)));
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
			$this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
		}
		if (!$this->_isEditable($path)) {
			$this->Session->setFlash(__(sprintf('Path %s is restricted', $path), true));
			$this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
		}
		$this->set('title_for_layout', sprintf(__('Edit file: %s'), $path));

		$pathE = explode(DS, $path);
		$n = count($pathE) - 1;
		$filename = $pathE[$n];
		unset($pathE[$n]);
		$path = implode(DS, $pathE);
		$this->file = new File($absolutefilepath, true);

		if (!empty($this->request->data) ) {
			if ($this->file->write($this->request->data['Filemanager']['content'])) {
				$this->Session->setFlash(__('File saved successfully'), 'default', array('class' => 'success'));
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
		$this->set('title_for_layout', __('Upload'));

		if (isset($this->request->query['path'])) {
			$path = $this->request->query['path'];
		} else {
			$path = APP;
		}
		$this->set(compact('path'));

		if (isset($this->request->data['Filemanager']['file']['tmp_name']) &&
			is_uploaded_file($this->request->data['Filemanager']['file']['tmp_name'])) {
			$destination = $path . $this->request->data['Filemanager']['file']['name'];
			move_uploaded_file($this->request->data['Filemanager']['file']['tmp_name'], $destination);
			$this->Session->setFlash(__('File uploaded successfully.'), 'default', array('class' => 'success'));
			$redirectUrl = Router::url(array('controller' => 'filemanager', 'action' => 'browse'), true) . '?path=' . urlencode($path);

			$this->redirect($redirectUrl);
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
			$this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
		}

		if (!$this->_isDeletable($path)) {
			$this->Session->setFlash(__(sprintf('Path %s is restricted', $path), true));
			$this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
		}

		if (file_exists($path) && unlink($path)) {
			$this->Session->setFlash(__('File deleted'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('An error occured'), 'default', array('class' => 'error'));
		}

		if (isset($_SERVER['HTTP_REFERER'])) {
			$this->redirect($_SERVER['HTTP_REFERER']);
		} else {
			$this->redirect(array('controller' => 'filemanager', 'action' => 'index'));
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
			$this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
		}

		if (is_dir($path) && rmdir($path)) {
			$this->Session->setFlash(__('Directory deleted'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('An error occured'), 'default', array('class' => 'error'));
		}

		if (isset($_SERVER['HTTP_REFERER'])) {
			$this->redirect($_SERVER['HTTP_REFERER']);
		} else {
			$this->redirect(array('controller' => 'filemanager', 'action' => 'index'));
		}

		exit();
	}

/**
 * Admin Rename
 *
 * @return void
 * @access public
 */
	public function admin_rename() {
		if (isset($this->request->query['path'])) {
			$path = $this->request->query['path'];
		} else {
			$this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
		}

		if (isset($this->request->query['newpath'])) {
			// rename here
		}

		if (isset($_SERVER['HTTP_REFERER'])) {
			$this->redirect($_SERVER['HTTP_REFERER']);
		} else {
			$this->redirect(array('controller' => 'filemanager', 'action' => 'index'));
		}
	}

/**
 * Admin Create Directory
 *
 * @return void
 * @access public
 */
	public function admin_create_directory() {
		$this->set('title_for_layout', __('New Directory'));

		if (isset($this->request->query['path'])) {
			$path = $this->request->query['path'];
		} else {
			$this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
		}

		if (!empty($this->request->data)) {
			$this->folder = new Folder;
			if ($this->folder->create($path . $this->request->data['Filemanager']['name'])) {
				$this->Session->setFlash(__('Directory created successfully.'), 'default', array('class' => 'success'));
				$redirectUrl = Router::url(array('controller' => 'filemanager', 'action' => 'browse'), true) . '?path=' . urlencode($path);
				$this->redirect($redirectUrl);
			} else {
				$this->Session->setFlash(__('An error occured'), 'default', array('class' => 'error'));
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
		$this->set('title_for_layout', __('New File'));

		if (isset($this->request->query['path'])) {
			$path = $this->request->query['path'];
		} else {
			$this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
		}

		if (!empty($this->request->data)) {
			if (touch($path . $this->request->data['Filemanager']['name'])) {
				$this->Session->setFlash(__('File created successfully.'), 'default', array('class' => 'success'));
				$redirectUrl = Router::url(array('controller' => 'filemanager', 'action' => 'browse'), true) . '?path=' . urlencode($path);
				$this->redirect($redirectUrl);
			} else {
				$this->Session->setFlash(__('An error occured'), 'default', array('class' => 'error'));
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
