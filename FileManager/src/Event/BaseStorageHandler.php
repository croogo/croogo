<?php

namespace Croogo\FileManager\Event;

use Cake\Core\App;
use Cake\Log\LogTrait;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use DOMDocument;
use Exception;
use InvalidArgumentException;

abstract class BaseStorageHandler
{

    use LogTrait;

    protected $_storage = null;

    /**
     * Instance config
     */
    protected $_config = [];

    /**
     * Constructor
     */
    public function __construct($config = [])
    {
        $name = get_class($this);
        $config = Hash::merge([
            'alias' => $name,
            'className' => $name,
        ], $config);
        $this->_config = $config;
        list($plugin, $storage) = pluginSplit(App::shortName($config['alias'], 'Event', 'StorageHandler'));
        $this->_storage = $storage;

        try {
            $this->Attachments = TableRegistry::get('Croogo/FileManager.Attachments');
        } catch (Exception $e) {
            $this->log(App::shortName(get_class($this), 'Event', 'StorageHandler') . ': ' . $e->getMessage(), LOG_CRIT);
        }
    }

    abstract protected function _parentAsset($attachment);

    protected function _check($event)
    {
        if (empty($event->getData('record')['adapter'])) {
            return false;
        }
        $return = $this->_storage == $event->getData('record')['adapter'];

        return $return;
    }

    public function storage()
    {
        return $this->_storage;
    }

    /**
     * Parse <img> tag and retrieves the value of the 'src' attribute
     */
    protected function _pathFromHtml($html)
    {
        if (!$html) {
            return;
        }
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        $imgTags = $doc->getElementsByTagName('img');
        if ($imgTags->length == 0) {
            return;
        }

        return $imgTags->item(0)->getAttribute('src');
    }

    /**
     * TODO: refactor this out and use Imagine in the future
     */
    protected function __getImageInfo($path)
    {
        if (!file_exists($path)) {
            return [];
        }

        $fp = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fp, $path);

        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
            case 'image/png':
            case 'image/gif':
                $size = getimagesize($path);
                list($width, $height) = $size;
                break;
            default:
                $width = $height = null;
                break;
        }

        return compact('width', 'height', 'mimeType');
    }

    /**
     * Registers a resized asset into the database
     *
     * Triggered by AssetsImageHelper::resize()
     */
    public function onResizeImage($Event)
    {
        if (!$this->_check($Event)) {
            return true;
        }
        if (!$Event->getData('record')) {
            return true;
        }

        $src = $this->_pathFromHtml($Event->getData('record')['result']);

        if (!$src) {
            return false;
        }

        try {
            $base = $Event->getSubject()->getRequest()->getAttribute('base');
            $filename = rtrim(WWW_ROOT, '/') . preg_replace(
                '/^' . preg_quote($base, '/') . '/',
                '',
                $src
            );
            $attachment = $this->Attachments->createFromFile($filename);
            if (is_string($attachment)) {
                return false;
            }
        } catch (InvalidArgumentException $e) {
            $this->log(get_class($this) . ': ' . $e->getMessage());
            throw $e;
        }

        return $this->_createAsset($attachment);
    }

    /**
     * Create AssetsAsset record from $attachment when necessary
     */
    protected function _createAsset($attachment)
    {
        $hash = $attachment->hash;
        $path = $attachment->import_path;
        $Assets = TableRegistry::get('Croogo/FileManager.Assets');
        $existing = $Assets->find()
            ->where([
                'OR' => [
                    $Assets->aliasField('hash') => $hash,
                    $Assets->aliasField('path') => $path,
                ],
            ])
            ->count();
        if ($existing > 0) {
            return false;
        }

        $parent = $this->_parentAsset($attachment);
        if (!$parent) {
            return false;
        }

        $asset = $Assets->newEntity([
            'parent_asset_id' => $parent->id,
            'model' => $parent->model,
            'foreign_key' => $parent->foreign_key,
            'adapter' => $parent->adapter,
            'path' => $path,
            'hash' => $hash,
        ]);

        return $Assets->save($asset);
    }
}
