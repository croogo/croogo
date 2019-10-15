<?php

namespace Croogo\FileManager\View\Helper;

use Cake\Utility\Hash;
use Croogo\Core\Croogo;
use Croogo\Core\View\Helper\ImageHelper;

class AssetsImageHelper extends ImageHelper
{

    public function resize($path, $width, $height, $options = [], $htmlAttributes = [], $return = false)
    {
        $filename = basename($path);
        $uploadsDir = dirname(basename($path));
        if ($uploadsDir === '.') {
            $uploadsDir = '';
        }
        $cacheDir = dirname($path);
        $options = Hash::merge([
            'aspect' => true,
            'adapter' => false,
            'cacheDir' => $cacheDir,
            'uploadsDir' => $uploadsDir,
        ], $options);
        $adapter = $options['adapter'];
        if ($adapter === 'LegacyLocalAttachment') {
            $options['cacheDir'] = 'resized';
            $options['resizedInd'] = '.resized-';
            $options['uploadsDir'] = 'uploads';
        }
        $result = parent::resize($path, $width, $height, $options, $htmlAttributes, $return);
        $record = compact('result', 'path', 'width', 'height', 'aspect', 'htmlAttributes', 'adapter');
        Croogo::dispatchEvent('Assets.AssetsImageHelper.resize', $this->_View, compact('record'));

        return $result;
    }

    /**
     * Looks upon $data and extract FeaturedImage tag or value
     *
     * By default, this method will return the generated <img> tag.  Pass
     * array('tag' => false) in the $options array to get the value.
     *
     * If you have multiple versions of image, you can retrieve a specific image
     * by passing an integer value in the `maxWidth` key.
     *
     * Example:
     *
     *  echo $this->AssetsImage->featured($node, array(
     *      'class' => 'gallery featured-image',
     *      'tag' => true,
     *      'maxWidth' => 500,
     *  ));
     *
     * @param array $data Array of record containing `LinkedAssets` key
     * @param array $options Array of options
     */
    public function featured($data, $options = [])
    {
        if (empty($data['LinkedAssets']['FeaturedImage'])) {
            return null;
        }
        $options = Hash::merge([
            'class' => 'featured-image',
            'tag' => true,
        ], $options);
        $tag = $options['tag'];
        $image = $data['LinkedAssets']['FeaturedImage'];
        $path = $image['path'];
        if (isset($options['maxWidth'])) {
            $maxWidth = $options['maxWidth'];
            unset($options['maxWidth']);
            if ($image['width'] > $maxWidth && !empty($image['Versions'])) {
                $found = false;
                foreach ($image['Versions'] as $version) {
                    $smallest = $version['path'];
                    if ($version['width'] <= $maxWidth) {
                        $path = $version['path'];
                        $found = true;
                        break;
                    }
                }
                if (!$found && isset($smallest)) {
                    $path = $smallest;
                }
            }
        }
        if ($tag) {
            return $this->Html->image($path, $options);
        } else {
            return $path;
        }
    }
}
