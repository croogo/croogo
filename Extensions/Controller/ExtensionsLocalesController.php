<?php

App::uses('File', 'Utility');
App::uses('Folder', 'Utility');
App::uses('ExtensionsAppController', 'Extensions.Controller');

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
class ExtensionsLocalesController extends ExtensionsAppController {

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
	public $uses = array(
		'Settings.Setting',
		'Users.User',
	);

/**
 * admin_index
 *
 * @return void
 */
	public function admin_index() {
		$this->set('title_for_layout', __d('croogo', 'Locales'));

		$locales = array();
		$folder =& new Folder;
		$paths = App::path('Locale');
		foreach ($paths as $path) {
			$folder->path = $path;
			$content = $folder->read();
			foreach ($content['0'] as $locale) {
				if (strstr($locale, '.') !== false) {
					continue;
				}
				if (!file_exists($path . $locale . DS . 'LC_MESSAGES' . DS . 'croogo.po')) {
					continue;
				}

				$locales[] = $locale;
			}
		}

		$this->set(compact('content', 'locales'));
	}

/**
 * admin_activate
 *
 * @param string $locale
 * @return void
 */
	public function admin_activate($locale = null) {
		$poFile = $this->__getPoFile($locale);
		if ($locale == null || !$poFile) {
			$this->Session->setFlash(__d('croogo', 'Locale does not exist.'), 'flash', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}

		$result = $this->Setting->write('Site.locale', $locale);
		if ($result) {
			$this->Session->setFlash(sprintf(__d('croogo', "Locale '%s' set as default"), $locale), 'flash', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Could not save Locale setting.'), 'flash', array('class' => 'error'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_add
 *
 * @return void
 */
	public function admin_add() {
		$this->set('title_for_layout', __d('croogo', 'Upload a new locale'));

		if ($this->request->is('post') && !empty($this->request->data)) {
			$file = $this->request->data['Locale']['file'];
			unset($this->request->data['Locale']['file']);

			// get locale name
			$zip = zip_open($file['tmp_name']);
			$locale = null;
			if ($zip) {
				while ($zipEntry = zip_read($zip)) {
					$zipEntryName = zip_entry_name($zipEntry);
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
				$this->Session->setFlash(__d('croogo', 'Invalid locale.'), 'flash', array('class' => 'error'));
				return $this->redirect(array('action' => 'add'));
			}

			if (is_dir(APP . 'Locale' . DS . $locale)) {
				$this->Session->setFlash(__d('croogo', 'Locale already exists.'), 'flash', array('class' => 'error'));
				return $this->redirect(array('action' => 'add'));
			}

			// extract
			$zip = zip_open($file['tmp_name']);
			if ($zip) {
				while ($zipEntry = zip_read($zip)) {
					$zipEntryName = zip_entry_name($zipEntry);
					if (strstr($zipEntryName, $locale . '/')) {
						$zipEntryNameE = explode($locale . '/', $zipEntryName);
						if (isset($zipEntryNameE['1'])) {
							$path = APP . 'Locale' . DS . $locale . DS . str_replace('/', DS, $zipEntryNameE['1']);
						} else {
							$path = APP . 'Locale' . DS . $locale . DS;
						}

						if (substr($path, strlen($path) - 1) == DS) {
							// create directory
							mkdir($path);
						} else {
							// create file
							if (zip_entry_open($zip, $zipEntry, 'r')) {
								$fileContent = zip_entry_read($zipEntry, zip_entry_filesize($zipEntry));
								touch($path);
								$fh = fopen($path, 'w');
								fwrite($fh, $fileContent);
								fclose($fh);
								zip_entry_close($zipEntry);
							}
						}
					}
				}
			}
			zip_close($zip);

			return $this->redirect(array('action' => 'index'));
		}
	}

/**
 * admin_edit
 *
 * @param string $locale
 * @return void
 */
	public function admin_edit($locale = null) {
		$this->set('title_for_layout', sprintf(__d('croogo', 'Edit locale: %s'), $locale));

		if (!$locale) {
			$this->Session->setFlash(__d('croogo', 'Invalid locale.'), 'flash', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}

		$poFile = $this->__getPoFile($locale);

		if (!$poFile) {
			$this->Session->setFlash(__d('croogo', 'The file %s does not exist.', 'croogo.po'), 'flash', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}

		$file =& new File($poFile, true);
		$content = $file->read();

		if (!empty($this->request->data)) {
			// save
			if ($file->write($this->request->data['Locale']['content'])) {
				$this->Session->setFlash(__d('croogo', 'Locale updated successfully'), 'flash', array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			}
		}

		$this->set(compact('locale', 'content'));
	}

/**
 * admin_delete
 *
 * @param string $locale
 * @return void
 */
	public function admin_delete($locale = null) {
		$poFile = $this->__getPoFile($locale);

		if (!$poFile) {
			$this->Session->setFlash(__d('croogo', 'The file %s does not exist.', 'croogo.po'), 'flash', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}

		$file =& new File($poFile, true);
		if ($file->delete()) {
			$this->Session->setFlash(__d('croogo', 'Locale deleted successfully.'), 'flash', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Local could not be deleted.'), 'flash', array('class' => 'error'));
		}

		return $this->redirect(array('action' => 'index'));
	}

	/**
	 * Returns the path to the croogo.po file
	 *
	 * @param $locale
	 */
	private function __getPoFile($locale) {
		$paths = App::path('Locale');
		foreach ($paths as $path) {
			$poFile = $path . $locale . DS . 'LC_MESSAGES' . DS . 'croogo.po';

			if (file_exists($poFile)) {
				return $poFile;
			}
		}

		return false;
	}

}
