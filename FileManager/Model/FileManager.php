<?php

App::uses('FileManagerAppModel', 'FileManager.Model');

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

	private function __isWithinPath($referencePath, $pathToCheck) {
		$path = realpath($pathToCheck);
		$regex = '/^' . preg_quote(realpath($referencePath), '/') . '/';
		return preg_match($regex, $path) > 0;
	}
}
