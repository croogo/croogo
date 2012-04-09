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

	public $deletablePaths = array();

	public function beforeFilter() {
		parent::beforeFilter();

		$this->deletablePaths = array(
			APP.'views'.DS.'themed'.DS,
			WWW_ROOT,
		);
		$this->set('deletablePaths', $this->deletablePaths);

		//App::import('Core', 'Folder');
		App::import('Core', 'File');
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

	public function admin_index() {
		$this->redirect(array('action' => 'browse'));
		die();
	}

	public function admin_browse() {
		$this->folder = new Folder;

		if (isset($this->params['url']['path'])) {
			$path = $this->params['url']['path'];
		} else {
			$path = APP;
		}

		$this->set('title_for_layout', __('File Manager', true));

		$path = realpath($path) . DS;
		$regex = '/^' . preg_quote(realpath(APP), '/') . '/';
		if (preg_match($regex, $path) == false) {
			$this->Session->setFlash(__(sprintf('Path %s is restricted', $path), true));
			$path = APP;
		}

		$blacklist = array('.git', '.svn', '.CVS');
		$regex = '/(' . implode('|', $blacklist) . ')/';
		if (in_array(basename($path), $blacklist) || preg_match($regex, $path)
			) {
			$this->Session->setFlash(__(sprintf('Path %s is restricted', $path), true));
			$path = dirname($path);
		}

		$this->folder->path = $path;

		$content = $this->folder->read();
		$this->set(compact('content'));
		$this->set('path', $path);
	}

	public function admin_editfile() {
		if (isset($this->params['url']['path'])) {
			$path = $this->params['url']['path'];
			$absolutefilepath = $path;
		} else {
			$this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
		}
		if (!$this->_isEditable($path)) {
			$this->Session->setFlash(__(sprintf('Path %s is restricted', $path), true));
			$this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
		}
		$this->set('title_for_layout', sprintf(__('Edit file: %s', true), $path));

		$path_e = explode(DS, $path);
		$n = count($path_e) - 1;
		$filename = $path_e[$n];
		unset($path_e[$n]);
		$path = implode(DS, $path_e);
		$this->file = new File($absolutefilepath, true);

		if (!empty($this->data) ) {
			if( $this->file->write($this->data['Filemanager']['content']) ) {
				$this->Session->setFlash(__('File saved successfully', true), 'default', array('class' => 'success'));
			}
		}

		$content = $this->file->read();

		$this->set(compact('content', 'path', 'absolutefilepath'));
	}

	public function admin_upload() {
		$this->set('title_for_layout', __('Upload', true));

		if (isset($this->params['url']['path'])) {
			$path = $this->params['url']['path'];
		} else {
			$path = APP;
		}
		$this->set(compact('path'));

		if (isset($this->data['Filemanager']['file']['tmp_name']) &&
			is_uploaded_file($this->data['Filemanager']['file']['tmp_name'])) {
			$destination = $path.$this->data['Filemanager']['file']['name'];
			move_uploaded_file($this->data['Filemanager']['file']['tmp_name'], $destination);
			$this->Session->setFlash(__('File uploaded successfully.', true), 'default', array('class' => 'success'));
			$redirectUrl = Router::url(array('controller' => 'filemanager', 'action' => 'browse'), true) . '?path=' . urlencode($path);

			$this->redirect($redirectUrl);
		}
	}

	public function admin_delete_file() {
		if (isset($this->params['url']['path'])) {
			$path = $this->params['url']['path'];
		} else {
			$this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
		}

		if (!$this->_isDeletable($path)) {
			$this->Session->setFlash(__(sprintf('Path %s is restricted', $path), true));
			$this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
		}

		if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
			$blackHoleCallback = $this->Security->blackHoleCallback;
			$this->$blackHoleCallback();
		}

		if (file_exists($path) && unlink($path)) {
			$this->Session->setFlash(__('File deleted', true), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('An error occured', true), 'default', array('class' => 'error'));
		}

		if (isset($_SERVER['HTTP_REFERER'])) {
			$this->redirect($_SERVER['HTTP_REFERER']);
		} else {
			$this->redirect(array('controller' => 'filemanager', 'action' => 'index'));
		}

		exit();
	}

	public function admin_delete_directory() {
		if (isset($this->params['url']['path'])) {
			$path = $this->params['url']['path'];
		} else {
			$this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
		}

		if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
			$blackHoleCallback = $this->Security->blackHoleCallback;
			$this->$blackHoleCallback();
		}

		if (is_dir($path) && rmdir($path)) {
			$this->Session->setFlash(__('Directory deleted', true), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('An error occured', true), 'default', array('class' => 'error'));
		}

		if (isset($_SERVER['HTTP_REFERER'])) {
			$this->redirect($_SERVER['HTTP_REFERER']);
		} else {
			$this->redirect(array('controller' => 'filemanager', 'action' => 'index'));
		}

		exit();
	}

	public function admin_rename() {
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

	public function admin_create_directory() {
		$this->set('title_for_layout', __('New Directory', true));

		if (isset($this->params['url']['path'])) {
			$path = $this->params['url']['path'];
		} else {
			$this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
		}

		if (!empty($this->data)) {
			$this->folder = new Folder;
			if ($this->folder->create($path . $this->data['Filemanager']['name'])) {
				$this->Session->setFlash(__('Directory created successfully.', true), 'default', array('class' => 'success'));
				$redirectUrl = Router::url(array('controller' => 'filemanager', 'action' => 'browse'), true) . '?path=' . urlencode($path);
				$this->redirect($redirectUrl);
			} else {
				$this->Session->setFlash(__('An error occured', true), 'default', array('class' => 'error'));
			}
		}

		$this->set(compact('path'));
	}

	public function admin_create_file() {
		$this->set('title_for_layout', __('New File', true));

		if (isset($this->params['url']['path'])) {
			$path = $this->params['url']['path'];
		} else {
			$this->redirect(array('controller' => 'filemanager', 'action' => 'browse'));
		}

		if (!empty($this->data)) {
			if (touch($path . $this->data['Filemanager']['name'])) {
				$this->Session->setFlash(__('File created successfully.', true), 'default', array('class' => 'success'));
				$redirectUrl = Router::url(array('controller' => 'filemanager', 'action' => 'browse'), true) . '?path=' . urlencode($path);
				$this->redirect($redirectUrl);
			} else {
				$this->Session->setFlash(__('An error occured', true), 'default', array('class' => 'error'));
			}
		}

		$this->set(compact('path'));
	}

	public function admin_chmod() {

	}

}
