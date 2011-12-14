<?php
/**
 * Filemanager Helper
 * 
 * PHP version 5
 *
 * @category Helper
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class FilemanagerHelper extends AppHelper {
/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
    public $helpers = array('Html');
/**
 * Get extension from a file name.
 *
 * @param string $filename file name
 * @return string
 */
    public function filename2ext($filename) {
        $filename = strtolower($filename) ;
        //$exts = split("[/\\.]", $filename) ;
        //$n = count($exts)-1;
        $filename_e = explode(".", $filename);
        if ($filename_e == 1) {
            return "file";
        } else {
            $n = count($filename_e) - 1;
            return $filename_e[$n];
        }
    }
/**
 * Get icon from file extension
 *
 * @param string $ext
 * @return string
 */
    public function ext2icon($ext) {
        $ext = strtolower($ext);

        $ext2icon = array(
            'css' => 'css.png',
            'htm' => 'html.png',
            'html' => 'html.png',
            'php' => 'page_white_php.png',

            'rar' => 'page_white_compressed.png',
            'tar' => 'page_white_compressed.png',
            'zip' => 'page_white_compressed.png',

            'bmp' => 'picture.png',
            'gif' => 'picture.png',
            'jpg' => 'picture.png',
            'jpeg' => 'picture.png',
            'png' => 'picture.png',
        );

        if (isset($ext2icon[$ext])) {
            $output = $ext2icon[$ext];
        } else {
            $output = 'page_white.png';
        }

        return $output;
    }
/**
 * Get icon from file name
 *
 * @param string $filename file name
 */
    public function filename2icon($filename) {
        $ext = $this->filename2ext($filename);
        $icon = $this->ext2icon($ext);
        return $icon;
    }
/**
 * Breadcrumb
 *
 * @param string $path absolute path
 * @return string
 */
    public function breadcrumb($path) {
        $path_e = explode(DS, $path);

        $output = array();
        if (DS == '/') {
            $current_path = DS;
        } else {
            $current_path = '';
        }
        foreach ($path_e AS $p) {
            if ($p != null) {
                $current_path .= $p.DS;
                $output[$p] = $current_path;
            }
        }

        return $output;
    }
/**
 * Generate anchor tag for a file/directory
 *
 * @param string $title link title
 * @param array $url link url
 * @param string $path file/directory path
 * @param string $pathKey default is 'path'
 * @return string
 */
    public function link($title, $url, $path, $pathKey = 'path') {
        $onclick = '';
        if (isset($url['action']) && ($url['action'] == 'delete_directory' || $url['action'] == 'delete_file')) {
            $onclick = 'return confirm(&#039;Are you sure?&#039;);';
        }
        $output = "<a onclick='{$onclick}' href='" . $this->Html->url($url) . "?{$pathKey}=" . urlencode($path) . "'>" . $title . "</a>";
        return $output;
    }
/**
 * Generate anchor tag for directory
 *
 * @param string $title link title
 * @param string $path directory path
 * @return string
 */
    public function linkDirectory($title, $path) {
        $output = $this->link($title, array('controller' => 'filemanager', 'action' => 'browse'), $path);
        return $output;
    }
/**
 * Generate anchor tag for file
 *
 * @param string $title
 * @param string $path
 * @return string
 */
    public function linkFile($title, $path) {
        $output = "<a href='" . $this->Html->url(array('controller' => 'filemanager', 'action' => 'editfile')) . "?path=" . urlencode($path) . "'>{$title}</a>";
        return $output;
    }
/**
 * Generate anchor tag for upload link
 *
 * @param string $title link title
 * @param string $path absolute path
 * @return string
 */
    public function linkUpload($title, $path) {
        $output = $this->link($title, array('controller' => 'filemanager', 'action' => 'upload'), $path);
        return $output;
    }
/**
 * Generate anchor tag for 'create a new directory' link
 *
 * @param string $title link title
 * @param string $path absolute path
 * @return string
 */
    public function linkCreateDirectory($title, $path) {
        $output = $this->link($title, array('controller' => 'filemanager', 'action' => 'new'), $path);
        return $output;
    }
/**
 * Get icon from mime type
 *
 * @param string $mimeType mine type
 * @return string
 */
    public function mimeTypeToImage($mimeType) {
        $mime = explode('/', $mimeType);
        $mime = $mime['0'];

        $mimeToImages = array();
        $mimeToImages['text'] = 'page_white.png';

        if (isset($mimeToImages[$mime])) {
            $output = $mimeToImages[$mime];
        } else {
            $output = 'page_white.png';
        }

        return $output;
    }
/**
 * Checks if searched location is under any of the paths
 *
 * @param array $paths
 * @param string $search
 * @return boolean
 */
    public function inPath($paths, $search) {
        foreach ($paths AS $path) {
            if (strpos($search, $path) !== false) {
                return true;
            }
        }
        return false;
    }
}

?>