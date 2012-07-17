<?php

App::uses('File', 'Utility');
APP::uses('Folder', 'Utility');

/**
 * Extensions Locales Controller
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
		$this->set('title_for_layout', __('Locales'));

		$folder =& new Folder;
		$folder->path = APP . 'Locale';
		$content = $folder->read();
		$locales = $content['0'];
		foreach ($locales as $i => $locale) {
			if (strstr($locale, '.') !== false) {
				unset($locales[$i]);
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
		if ($locale == null || !is_dir(APP . 'Locale' . DS . $locale)) {
			$this->Session->setFlash(__('Locale does not exist.'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		$result = $this->Setting->write('Site.locale', $locale);
		if ($result) {
			$this->Session->setFlash(sprintf(__("Locale '%s' set as default"), $locale), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('Could not save Locale setting.'), 'default', array('class' => 'error'));
		}
		$this->redirect(array('action' => 'index'));
	}

/**
 * admin_add
 *
 * @return void
 */
	public function admin_add() {
		$this->set('title_for_layout', __('Upload a new locale'));

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
				$this->Session->setFlash(__('Invalid locale.'), 'default', array('class' => 'error'));
				$this->redirect(array('action' => 'add'));
			}

			if (is_dir(APP . 'Locale' . DS . $locale)) {
				$this->Session->setFlash(__('Locale already exists.'), 'default', array('class' => 'error'));
				$this->redirect(array('action' => 'add'));
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

			$this->redirect(array('action' => 'index'));
		}
	}

/**
 * admin_edit
 *
 * @param string $locale
 * @return void
 */
	public function admin_edit($locale = null) {
		$this->set('title_for_layout', sprintf(__('Edit locale: %s'), $locale));

		if (!$locale) {
			$this->Session->setFlash(__('Invalid locale.'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		if (!file_exists(APP . 'Locale' . DS . $locale . DS . 'LC_MESSAGES' . DS . 'default.po')) {
			$this->Session->setFlash(__('The file default.po does not exist.'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		$file =& new File(APP . 'Locale' . DS . $locale . DS . 'LC_MESSAGES' . DS . 'default.po', true);
		$content = $file->read();

		if (!empty($this->request->data)) {
			// save
			if ($file->write($this->request->data['Locale']['content'])) {
				$this->Session->setFlash(__('Locale updated successfully'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
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
		if (!$locale) {
			$this->Session->setFlash(__('Invalid locale'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		$folder =& new Folder;
		if ($folder->delete(APP . 'Locale' . DS . $locale)) {
			$this->Session->setFlash(__('Locale deleted successfully.'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('Local could not be deleted.'), 'default', array('class' => 'error'));
		}

		$this->redirect(array('action' => 'index'));
	}

}
