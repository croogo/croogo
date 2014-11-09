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

	public function isEditable($path) {
		$editablePaths = Configure::check('FileManager.editablePaths') ?
			Configure::read('FileManager.editablePaths') : APP;

		foreach ($editablePaths as $editablePath) {
			if ($this->__isWithinPath($editablePath, $path)) {
				return true;
			}
		}

		return false;
	}

	public function rename($oldPath, $newPath) {
		if (is_dir($oldPath)) {
			$Folder = new Folder($oldPath);
			return $Folder->move(array('from' => $oldPath, 'to' => $newPath));
		} else {
			return rename($oldPath, $newPath);
		}
	}

	private function __isWithinPath($referencePath, $pathToCheck) {
		$path = realpath($pathToCheck);
		$regex = '/^' . preg_quote(realpath($referencePath), '/') . '/';
		return preg_match($regex, $path) > 0;
	}
}
