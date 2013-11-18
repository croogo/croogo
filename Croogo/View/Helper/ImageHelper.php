<?php

App::uses('Helper', 'View/Helper');

/**
 * @package Croogo.Croogo.View.Helper
 * @version 1.1
 * @author Josh Hundley
 * @author Jorge Orpinel <jop@levogiro.net> (changes)
 */
class ImageHelper extends Helper {
	public $helpers = array('Html');
	public $cacheDir = 'resized'; // relative to 'img'.DS

	/**
	 * Automatically resizes an image and returns formatted IMG tag
	 *
	 * @param string $path Path to the image file, relative to the webroot/img/ directory.
	 * @param integer $width Image of returned image
	 * @param integer $height Height of returned image
	 * @param boolean $aspect Maintain aspect ratio (default: true)
	 * @param array	$htmlAttributes Array of HTML attributes.
	 * @param boolean $return Wheter this method should return a value or output it. This overrides AUTO_OUTPUT.
	 * @return mixed  Either string or echos the value, depends on AUTO_OUTPUT and $return.
	 * @access public
	 */
	public function resize($path, $width, $height, $options = array(), $htmlAttributes = array(), $return = false) {
		if (is_bool($options)) {
			$options = array('aspect' => $options);
		}
		$options = Hash::merge(array(
			'aspect' => true,
			'uploadsDir' => 'uploads',
			'cacheDir' => $this->cacheDir,
			'resizedInd' => '.resized-',
		), $options);
		$aspect = $options['aspect'];
		$uploadsDir = $options['uploadsDir'];
		$cacheDir = $options['cacheDir'];
		$resizedInd = $options['resizedInd'];
		$types = array(1 => "gif", "jpeg", "png", "swf", "psd", "wbmp"); // used to determine image type
		$transparency = array("gif", "png");	// image types with transparency
		if (empty($htmlAttributes['alt'])) $htmlAttributes['alt'] = 'thumb';  // Ponemos alt default

		$url = WWW_ROOT . DS . $path;

		if (!file_exists($url)) {
			return; // image doesn't exist
		}

		$size = getimagesize($url);

		if ($aspect) { // adjust to aspect.
			if (($size[1]/$height) > ($size[0]/$width))  // $size[0]:width, [1]:height, [2]:type
				$width = ceil(($size[0]/$size[1]) * $height);
			else
				$height = ceil($width / ($size[0]/$size[1]));
		}

		$dimension = $resizedInd . $width . 'x' . $height;
		$parts = pathinfo(WWW_ROOT . $path);
		if ($resizedInd === 'resized') {
			// legacy format
			$resized = $width . 'x' . $height . '_' . $parts['filename'] . '.' . $parts['extension'];
		} elseif (strpos($path, $resizedInd) === false) {
			$resized = $parts['filename'] . $dimension . '.' . $parts['extension'];
		} else {
			$resized = $parts['filename'] . '.' . $parts['extension'];
		}
		$relfile = '/';
		if ($uploadsDir) {
			$relfile .= ltrim($uploadsDir, '/') . '/';
		}
		if ($cacheDir) {
			$relfile .= ltrim($cacheDir, '/') . '/';
		}
		$relfile .= $resized;
		$cachefile = WWW_ROOT . ltrim($relfile, '/');

		if (file_exists($cachefile)) {
			$csize = getimagesize($cachefile);
			$cached = ($csize[0] == $width && $csize[1] == $height); // image is cached
			if (@filemtime($cachefile) < @filemtime($url)) // check if up to date
				$cached = false;
		} else {
			$cached = false;
		}

		if (!$cached) {
			$resize = ($size[0] > $width || $size[1] > $height) || ($size[0] < $width || $size[1] < $height);
		} else {
			$resize = false;
		}

		if ($resize) {
			$image = call_user_func('imagecreatefrom'.$types[$size[2]], $url);
			if (function_exists("imagecreatetruecolor") && ($temp = imagecreatetruecolor ($width, $height))) {
				if (in_array($types[$size[2]],$transparency)) {
					imagealphablending($temp, false);
					imagesavealpha($temp,true);
					$transparent = imagecolorallocatealpha($temp, 255, 255, 255, 127);
					imagefilledrectangle($temp, 0, 0, $width, $height, $transparent);
				}
				imagecopyresampled ($temp, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
			} else {
				$temp = imagecreate ($width, $height);
				if (in_array($types[$size[2]],$transparency)) {
					imagealphablending($temp, false);
					imagesavealpha($temp,true);
					$transparent = imagecolorallocatealpha($temp, 255, 255, 255, 127);
					imagefilledrectangle($temp, 0, 0, $width, $height, $transparent);
				}
				imagecopyresized ($temp, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
			}
			call_user_func("image".$types[$size[2]], $temp, $cachefile);
			imagedestroy ($image);
			imagedestroy ($temp);
		} else {
			//copy($url, $cachefile);
		}

		return $this->output(sprintf($this->Html->_tags['image'], $relfile, $this->Html->_parseAttributes($htmlAttributes, null, '', ' ')), $return);
	}
}
