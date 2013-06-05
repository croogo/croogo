<?php

App::uses('AppHelper', 'View/Helper');

/**
 * FileManager Helper
 *
 * PHP version 5
 *
 * @category Helper
 * @package  Croogo.FileManager.View.Helper
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class FileManagerHelper extends AppHelper {

/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
	public $helpers = array('Html', 'Form');

/**
 * Get extension from a file name.
 *
 * @param string $filename file name
 * @return string
 */
	public function filename2ext($filename) {
		$filename = strtolower($filename);
		$filenameE = explode(".", $filename);
		if ($filenameE == 1) {
			return "file";
		} else {
			$n = count($filenameE) - 1;
			return $filenameE[$n];
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

		$extToIcon = array(
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

		if (isset($extToIcon[$ext])) {
			$output = $extToIcon[$ext];
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
		$pathE = explode(DS, $path);

		$output = array();
		if (DS == '/') {
			$currentPath = DS;
		} else {
			$currentPath = '';
		}
		foreach ($pathE as $p) {
			if ($p != null) {
				$currentPath .= $p . DS;
				$output[$p] = $currentPath;
			}
		}

		return $output;
	}

/**
 * adminAction
 */
	public function adminAction($title, $url, $path, $pathKey = 'path') {
		return $this->Html->tag('li', $this->link($title, $url, $path, $pathKey));
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
		$class = '';
		if (isset($url['action'])
			&& ($url['action'] == 'create_directory' || $url['action'] == 'upload' || $url['action'] == 'create_file')) {
			$class = 'btn';
		}

		if (isset($url['action']) && ($url['action'] == 'delete_directory' || $url['action'] == 'delete_file')) {
			$output = $this->Form->postLink($title, $url, array('data' => compact('path')), __d('croogo', 'Are you sure?'));
		} else {
			$output = '<a class="' . $class . '" href="' . $this->Html->url($url) . "?{$pathKey}=" . urlencode($path) . '">' . $title . '</a>';
		}
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
		$output = $this->link($title, array(
			'plugin' => 'file_manager',
			'controller' => 'file_manager',
			'action' => 'browse',
		), $path);
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
		$output = "<a href='" . $this->Html->url(array('controller' => 'file_manager', 'action' => 'editfile')) . "?path=" . urlencode($path) . "'>{$title}</a>";
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
		$output = $this->link($title, array('controller' => 'file_manager', 'action' => 'upload'), $path);
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
		$output = $this->link($title, array('controller' => 'file_manager', 'action' => 'new'), $path);
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
		foreach ($paths as $path) {
			if (strpos($search, $path) !== false) {
				return true;
			}
		}
		return false;
	}
}
