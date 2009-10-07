<?php
/**
 * Theme Component
 *
 * PHP version 5
 *
 * @category Component
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ThemeComponent extends Object {

    function startup(&$controller) {
        $this->controller =& $controller;
        App::import('Core', 'File');
        App::import('Xml');
    }
/**
 * Get theme alises (folder names)
 *
 * @return array
 */
    function getThemes() {
        $themes = array();
        $this->folder = new Folder;
        $this->folder->path = WWW_ROOT . 'themed';
        $themeFolders = $this->folder->read();
        foreach ($themeFolders[0] AS $themeFolder) {
            if (substr($themeFolder, 0, 1) != '.') {
                $this->folder->path = WWW_ROOT . 'themed' . DS . $themeFolder;
                $themeFolderContent = $this->folder->read();
                if (in_array('theme.xml', $themeFolderContent[1])) {
                    $themes[] = $themeFolder;
                }
            }
        }
        return $themes;
    }
/**
 * Get the content of theme.xml file
 *
 * @param string $alias theme folder name
 * @return array
 */
    function getData($alias = null) {
        if ($alias == null) {
            $themeXml =& new XML(WWW_ROOT . 'theme.xml');
        } else {
            $themeXml =& new XML(WWW_ROOT . 'themed' . DS . $alias . DS . 'theme.xml');
        }
        $themeData = Set::reverse($themeXml);
        return $themeData;
    }

}
?>