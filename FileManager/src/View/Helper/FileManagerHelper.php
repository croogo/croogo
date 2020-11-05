<?php
declare(strict_types=1);

namespace Croogo\FileManager\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use Croogo\FileManager\Utility\FileManager;

/**
 * FileManager Helper
 *
 * @category Helper
 * @package  Croogo.FileManager.View.Helper
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\FormHelper $Form
 */
class FileManagerHelper extends Helper
{

    /**
     * Other helpers used by this helper
     *
     * @var array
     * @access public
     */
    public $helpers = ['Html', 'Form'];

    private $__actionsAsButton = [
        'upload',
        'create_directory',
        'create_file'
    ];

    private $__postLinkActions = [
        'delete_directory',
        'delete_file'
    ];

    /**
     * Instance of FileManager utility class
     *
     * @var \Croogo\FileManager\Utility\FileManager
     */
    protected $FileManager;

    /**
     * {@inheritdoc}
     */
    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);
        $this->FileManager = new FileManager();
    }

    /**
     * Get extension from a file name.
     *
     * @param string $filename file name
     * @return string
     */
    public function filename2ext($filename)
    {
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
     * @param string $ext Extension
     * @return string Icon
     */
    public function ext2icon($ext)
    {
        $ext = strtolower($ext);

        $extToIcon = [
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
        ];

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
     * @return string Icon
     */
    public function filename2icon($filename)
    {
        $ext = $this->filename2ext($filename);
        $icon = $this->ext2icon($ext);

        return $icon;
    }

    public function filename2mime($filename)
    {
        return $this->FileManager->filename2mime($filename);
    }

    /**
     * Breadcrumb
     *
     * @param string $path absolute path
     * @return string
     */
    public function breadcrumb($path)
    {
        $pathE = explode(DS, $path);

        $output = [];
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
     *
     * @param string $title Title
     * @param string|array $url Url
     * @param string $path Path
     * @param string $pathKey Query string variable name denoting path
     * @return string Action link
     */
    public function adminAction($title, $url, $path, $pathKey = 'path')
    {
        return $this->link($title, $url, $path, $pathKey);
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
    public function link($title, $url, $path, $pathKey = 'path')
    {
        $class = '';
        if (isset($url['action']) && in_array($url['action'], $this->__actionsAsButton)) {
            $class = 'btn btn-outline-secondary btn-sm';
        }

        if (isset($url['action']) && in_array($url['action'], $this->__postLinkActions)) {
            $output = $this->Form->postLink($title, $url, ['data' => compact('path'), 'escape' => true], __d('croogo', 'Are you sure?'));
        } else {
            $url[$pathKey] = $path;
            $output = $this->Html->link($title, $url, [
                'class' => $class,
            ]);
        }

        return $output;
    }

    /**
     * Generate anchor tag for directory
     *
     * @param string $title link title
     * @param string $path directory path
     * @param array $url url
     * @return string
     */
    public function linkDirectory($title, $path, $url = [])
    {
        $output = $this->link($title, array_merge([
            'plugin' => 'Croogo/FileManager',
            'controller' => 'FileManager',
            'action' => 'browse',
        ], $url), $path);

        return $output;
    }

    /**
     * Generate anchor tag for file
     *
     * @param string $title Title
     * @param string $path File path
     * @param array $url url
     * @return string
     */
    public function linkFile($title, $path, $url = [])
    {
        return $this->Html->link($title, array_merge([
            'controller' => 'FileManager',
            'action' => 'editFile',
            '?' => [
                'path' => $path,
            ],
        ], $url));
    }

    /**
     * Generate anchor tag for upload link
     *
     * @param string $title link title
     * @param string $path absolute path
     * @param array $url url
     * @return string
     */
    public function linkUpload($title, $path, $url = [])
    {
        $output = $this->link($title, array_merge([
            'controller' => 'FileManager',
            'action' => 'upload',
        ], $url), $path);

        return $output;
    }

    /**
     * Generate anchor tag for 'create a new directory' link
     *
     * @param string $title link title
     * @param string $path absolute path
     * @return string
     */
    public function linkCreateDirectory($title, $path, $url = [])
    {
        $output = $this->link($title, array_merge([
            'controller' => 'FileManager',
            'action' => 'new',
        ], $url), $path);

        return $output;
    }

    /**
     * Get icon from mime type
     *
     * @param string $mimeType mine type
     * @return string
     */
    public function mimeTypeToImage($mimeType)
    {
        $mime = explode('/', $mimeType);
        $mime = $mime['0'];

        $mimeToImages = [];
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
     * @param array $paths Paths
     * @param string $search Search string
     * @return bool
     */
    public function inPath($paths, $search)
    {
        foreach ($paths as $path) {
            if (strpos($search, $path) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @see \Croogo\FileManager\Utility\FileManager::isEditable()
     */
    public function isEditable($path) {
        return $this->FileManager->isEditable($path);
    }

    /**
     * @see \Croogo\FileManager\Utility\FileManager::isDeletable()
     */
    public function isDeletable($path) {
        return $this->FileManager->isDeletable($path);
    }
}
