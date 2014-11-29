<?php

App::uses('FileManagerAppModel', 'FileManager.Model');
App::uses('File', 'Utility');
/**
 * FileManager Model
 *
 * @category FileManager.Model
 * @package  Croogo.FileManager.Model
 * @version  2.1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class FileManager extends FileManagerAppModel {

	public $useTable = false;

	public $defaultBrowsingPath;
	public $defaultEditablePaths = array();
	public $defaultDeletablePaths = array();

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->defaultBrowsingPath = APP . DS .  WEBROOT_DIR .DS . 'uploads';
	}

/**
 * Checks wether given $path is editable
 *
 * A file is editable when it resides under directories registered in
 * FileManager.editablePaths
 *
 * @param string $path Path to check
 * @return boolean true if file is editable
 */
	public function isEditable($path) {
		foreach ($this->getEditablePaths() as $editablePath) {
			if ($this->_isWithinPath($editablePath, $path)) {
				return true;
			}
		}

		return false;
	}

/**
 * Checks wether given $path is deletable
 *
 * A file is deleteable when it resides under directories registered in
 * FileManager.deletablePaths
 *
 * @param string $path Path to check
 * @return boolean true when file is deletable
 */
	public function isDeletable($path) {
		foreach ($this->getDeletablePaths() as $deletablePath) {
			if ($this->_isWithinPath($deletablePath, $path)) {
				return true;
			}
		}

		return false;
	}

/**
 * Rename $oldPath to $newPath
 *
 * @param string $oldPath Old filename/directory
 * @param string $newPath New filename/directory
 * @return bool True if rename was successful
 */
	public function rename($oldPath, $newPath) {
		if (is_dir($oldPath)) {
			$Folder = new Folder($oldPath);
			return $Folder->move(array('from' => $oldPath, 'to' => $newPath));
		} else {
			return rename($oldPath, $newPath);
		}
	}

/**
 * Checks that $pathToCheck resides under $referencePath
 *
 * @param string $referencePath Reference path
 * @param string $pathToCheck Path to check
 * @return boolean True if $pathToCheck resides under $referencePath
 */
	protected function _isWithinPath($referencePath, $pathToCheck) {
		$path = realpath($pathToCheck);
		$regex = '/^' . preg_quote(realpath($referencePath), '/') . '/';
		return preg_match($regex, $path) > 0;
	}

/**
 * @return array|mixed editable (writable) paths
 */
	public function getEditablePaths() {
		return Configure::check('FileManager.editablePaths') ?
			Configure::read('FileManager.editablePaths') :
			$this->defaultEditablePaths;
	}

/**
 * @return array|mixed deletable paths
 */
	public function getDeletablePaths () {
		return Configure::check('FileManager.deletablePaths') ?
			Configure::read('FileManager.deletablePaths') :
			$this->defaultDeletablePaths;
	}

/**
 * @default
 * @return mixed|string Return the default path when browsing folder
 */
	public function getDefaultBrowsingPath() {
		return Configure::check('FileManager.defaultBrowsePath') ?
			Configure::read('FileManager.defaultBrowsePath') :
			$this->defaultBrowsingPath;
	}
}
