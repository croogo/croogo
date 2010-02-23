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
        $themes = array('default');
        $this->folder = new Folder;
        $this->folder->path = APP . 'views' . DS . 'themed';
        $themeFolders = $this->folder->read();
        foreach ($themeFolders[0] AS $themeFolder) {
            if (substr($themeFolder, 0, 1) != '.') {
                $this->folder->path = APP . 'views' . DS . 'themed' . DS . $themeFolder . DS . 'webroot';
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
        if ($alias == null || $alias == 'default') {
            $xmlLocation = WWW_ROOT . 'theme.xml';
        } else {
            $xmlLocation = APP . 'views' . DS . 'themed' . DS . $alias . DS . 'webroot' . DS . 'theme.xml';
            if (!file_exists($xmlLocation)) {
                $xmlLocation = WWW_ROOT . 'theme.xml';
            }
        }
        $themeXml =& new XML($xmlLocation);
        $themeData = Set::reverse($themeXml);
        return $themeData;
    }

}
?>