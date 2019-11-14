<?php

namespace Croogo\FileManager\Utility;

use Cake\Core\Configure;
use Cake\Filesystem\Folder;

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
class FileManager
{

    /**
     * Checks wether given $path is editable
     *
     * A file is editable when it resides under directories registered in
     * FileManager.editablePaths
     *
     * @param string $path Path to check
     * @return bool true if file is editable
     */
    public function isEditable($path)
    {
        $editablePaths = (array)Configure::check('FileManager.editablePaths');
        foreach ($editablePaths as $editablePath) {
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
     * @return bool true when file is deletable
     */
    public function isDeletable($path)
    {
        $deletablePaths = (array)Configure::read('FileManager.deletablePaths');
        foreach ($deletablePaths as $deletablePath) {
            if ($deletablePath == $path) {
                continue;
            }
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
    public function rename($oldPath, $newPath)
    {
        if (is_dir($oldPath)) {
            $Folder = new Folder($oldPath);

            return $Folder->move(['from' => $oldPath, 'to' => $newPath]);
        } else {
            return rename($oldPath, $newPath);
        }
    }

    /**
     * Checks that $pathToCheck resides under $referencePath
     *
     * @param string $referencePath Reference path
     * @param string $pathToCheck Path to check
     * @return bool True if $pathToCheck resides under $referencePath
     */
    protected function _isWithinPath($referencePath, $pathToCheck)
    {
        $path = realpath($pathToCheck);
        $regex = '/^' . preg_quote(realpath($referencePath), '/') . '/';

        return preg_match($regex, $path) > 0;
    }
}
