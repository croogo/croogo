<?php

namespace Croogo\Core\View\Helper;

use Cake\Utility\Hash;
use Cake\View\Helper\HtmlHelper;

/**
 * @package Croogo.Croogo.View.Helper
 * @version 1.1
 * @author Josh Hundley
 * @author Jorge Orpinel <jop@levogiro.net> (changes)
 */
class ImageHelper extends HtmlHelper
{

    public array $helpers = [
        'Html',
        'Theme',
        'Url',
    ];

    /**
     * Automatically resizes an image and returns formatted IMG tag
     *
     * Options:
     * - aspect Maintain aspect ratio. Default: true
     * - uploadsDir Upload directory name. Default: 'uploads'
     * - cachedir Cache directory name. Default: 'resized'
     * - resizeInd: String to check in filename indicating that it was resized
     *
     * @param string $path Path to the image file, relative to the webroot/img/ directory.
     * @param int $width of returned image
     * @param int $height of returned image
     * @param array $options Options
     * @param array $htmlAttributes Array of HTML attributes.
     * @param bool $return this method should return a value or output it. This overrides AUTO_OUTPUT.
     * @return mixed Either string or echoes the value, depends on AUTO_OUTPUT and $return.
     * @access public
     */
    public function resize($path, $width, $height, $options = [], $htmlAttributes = [], $return = false)
    {
        if (is_bool($options)) {
            $options = ['aspect' => $options];
        }
        $options = Hash::merge([
            'aspect' => true,
            'uploadsDir' => 'uploads',
            'cacheDir' => 'resized',
            'resizedInd' => '.resized-',
            'templates' => []
        ], $options);
        $aspect = $options['aspect'];
        $uploadsDir = $options['uploadsDir'];
        $cacheDir = $options['cacheDir'];
        $resizedInd = $options['resizedInd'];
        $imgClass = $this->Theme->getCssClass('thumbnailClass');

        if (empty($htmlAttributes['alt'])) {
            $htmlAttributes['alt'] = 'thumb';
        }

        if (!array_key_exists('class', $htmlAttributes)) {
            $htmlAttributes['class'] = $imgClass;
        }

        $sourcefile = WWW_ROOT . DS . $path;

        if (!file_exists($sourcefile)) {
            return;
        }

        // A truncated, empty or non-image upload makes getimagesize() return false, and the aspect
        // maths below would then divide by zero. Returning null matches the missing-source case
        // above, so callers already handle it. The read notice is suppressed because a broken
        // upload is an expected condition here, not something worth one error-log entry per render.
        // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
        $size = @getimagesize($sourcefile);

        if (!$size || empty($size[0]) || empty($size[1])) {
            return;
        }

        if ($aspect) {
            if (($size[1] / $height) > ($size[0] / $width)) {
                $width = ceil(($size[0] / $size[1]) * $height);
            } else {
                $height = ceil($width / ($size[0] / $size[1]));
            }
        }

        $dimension = $resizedInd . $width . 'x' . $height;
        $parts = pathinfo(WWW_ROOT . $path);
        // pathinfo() omits `extension` entirely for a name carrying no dot, while file_exists()
        // above has already proven the source is really there - so an extension-less upload warned
        // on every render and built a cache name ending in a bare dot. Carry the separator with
        // the extension so the normal case is byte-identical and the odd one simply has neither.
        $extension = isset($parts['extension']) ? '.' . $parts['extension'] : '';
        if ($resizedInd === '') {
            // legacy format
            $filename = $parts['filename'];
            $filename = preg_replace('/^[0-9]*x[0-9]*_/', '', $filename);
            $resized = $width . 'x' . $height . '_' . $filename . $extension;
        } else {
            $filename = $parts['filename'];
            $filename = preg_replace('/' . preg_quote($resizedInd) . '[0-9]*x[0-9]*/', '', $filename);
            $resized = $filename . $dimension . $extension;
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

        $targetDir = dirname($cachefile);
        if (!is_dir($targetDir)) {
            mkdir($targetDir);
        }

        $cached = false;
        if (file_exists($cachefile)) {
            $csize = getimagesize($cachefile);

            // image is cached
            $cached = ($csize[0] == $width && $csize[1] == $height);

            // check if up to date
            if (filemtime($cachefile) < filemtime($sourcefile)) {
                $cached = false;
            }
        }

        if (!$cached) {
            $resize = ($size[0] > $width || $size[1] > $height) || ($size[0] < $width || $size[1] < $height);
        } else {
            $resize = false;
        }

        if ($resize) {
            $this->_resize($sourcefile, $size, $cachefile, $width, $height);
        } elseif (!file_exists($cachefile)) {
            copy($sourcefile, $cachefile);
        }

        $templater = $this->templater();
        $newTemplates = $options['templates'];

        if ($newTemplates) {
            $templater->push();
            $templateMethod = is_string($options['templates']) ? 'load' : 'add';
            $templater->{$templateMethod}($options['templates']);
        }
        unset($options['templates']);

        return $templater->format('image', [
            'url' => $this->Url->webroot($relfile),
            'attrs' => $templater->formatAttributes($htmlAttributes),
        ]);
    }

    /**
     * GD reader/writer suffix per IMAGETYPE_* constant.
     *
     * Only formats GD can actually round-trip belong here. The list is deliberately shorter than
     * the IMAGETYPE_* family: TIFF, ICO, PSD and SWF have no imagecreatefrom*() at all, so
     * _resize() copies the original for them rather than guessing a function name.
     *
     * @var array<int, string>
     */
    protected const RESIZE_FORMATS = [
        IMAGETYPE_GIF => 'gif',
        IMAGETYPE_JPEG => 'jpeg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_BMP => 'bmp',
        IMAGETYPE_WBMP => 'wbmp',
        IMAGETYPE_XBM => 'xbm',
        IMAGETYPE_WEBP => 'webp',
        IMAGETYPE_AVIF => 'avif',
    ];

    /**
     * Convenience method to resize image
     *
     * The format is derived from the IMAGETYPE_* constant reported by getimagesize(), never from
     * the file name - an upload is routinely stored under an extension that does not match its
     * real content. When the format is one GD cannot read here, the original is copied to the
     * target unchanged: an oversized thumbnail beats a fatal error on the whole page.
     *
     * @param string $source File name of the source image
     * @param array $sourceSize Result of getimagesize() against $source
     * @param string $target File name of the target image
     * @param int $w Target Image width
     * @param int $h Target image height
     * @return void
     */
    protected function _resize($source, $sourceSize, $target, $w, $h)
    {
        $transparency = ['gif', 'png', 'webp'];

        $format = static::RESIZE_FORMATS[$sourceSize[2]] ?? null;
        $reader = $format === null ? null : 'imagecreatefrom' . $format;
        $writer = $format === null ? null : 'image' . $format;

        // An unknown IMAGETYPE, or a GD build compiled without this format (WebP and AVIF are both
        // optional), leaves no function to call, so the name is validated before it ever reaches
        // call_user_func() - an invalid callback there is a fatal, not a recoverable warning.
        $image = $reader !== null && function_exists($reader) && function_exists($writer)
            ? call_user_func($reader, $source)
            : false;

        if (!$image) {
            if (is_writable(dirname($target))) {
                copy($source, $target);
            }

            return;
        }

        $sw = $sourceSize[0];
        $sh = $sourceSize[1];

        if (function_exists('imagecreatetruecolor')) {
            $temp = imagecreatetruecolor($w, $h);
            if (in_array($format, $transparency)) {
                $this->_setupTransparency($temp, $w, $h);
            }
            imagecopyresampled($temp, $image, 0, 0, 0, 0, $w, $h, $sw, $sh);
        } else {
            $temp = imagecreate($w, $h);
            if (in_array($format, $transparency)) {
                $this->_setupTransparency($temp, $w, $h);
            }
            imagecopyresized($temp, $image, 0, 0, 0, 0, $w, $h, $sw, $sh);
        }
        if (is_writable(dirname($target))) {
            call_user_func($writer, $temp, $target);
        }
        // No imagedestroy() here: GdImage has been garbage-collected since PHP 8.0, where the call
        // became a no-op, and PHP 8.5 deprecates it - two log entries per rendered thumbnail.
    }

    /**
     * Convenience method to setup image transparency
     *
     * @param resource $image Image resource
     * @param int $w Width
     * @param int $h Height
     * @return void
     */
    protected function _setupTransparency($image, $w, $h)
    {
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
        imagefilledrectangle($image, 0, 0, $w, $h, $transparent);
    }
}
