<?php

namespace Croogo\FileManager\Event;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\LogTrait;
use Croogo\FileManager\Utility\StorageManager;

class LegacyLocalAttachmentStorageHandler extends BaseStorageHandler implements EventListenerInterface {

    use LogTrait;

    public function implementedEvents() {
        return array(
            'FileStorage.beforeSave' => 'onBeforeSave',
            'FileStorage.beforeDelete' => 'onBeforeDelete',
            'Assets.AssetsImageHelper.resize' => 'onResizeImage',
        );
    }

    public function onBeforeSave($Event) {
        if (!$this->_check($Event)) {
            return true;
        }

        $model = $Event->getSubject();
        $storage =& $event->data['record'];

        if (empty($storage->file)) {
            if (isset($storage->path) && empty($storage->filename)) {
                $path = rtrim(WWW_ROOT, '/') . $storage->path;
                $fp = fopen($path, 'r');
                $stat = fstat($fp);
                $imageInfo = $this->__getImageInfo($path);
                $storage['filesize'] = $stat[7];
                $storage['filename'] = basename($path);
                $storage['hash'] = sha1_file($path);
                $storage['mime_type'] = $imageInfo['mimeType'];
                $storage['width'] = $imageInfo['width'];
                $storage['height'] = $imageInfo['height'];
                $storage['extension'] = substr($path, strrpos($path, '.') + 1);
            }
            return true;
        }

        $file = $storage->file;
        $adapter = StorageManager::adapter($storage->adapter);
        try {
            $raw = file_get_contents($file['tmp_name']);
            $extension = substr($file['name'], strrpos($file['name'], '.') + 1);

            $imageInfo = $this->__getImageInfo($file['tmp_name']);
            if (isset($imageInfo['mimeType'])) {
                $mimeType = $imageInfo['mimeType'];
            } else {
                $mimeType = $file['type'];
            }

            $result = $adapter->write($file['name'], $raw);
            $storage['filename'] = $file['name'];
            $storage['filesize'] = $file['size'];
            $storage['hash'] = sha1($raw);
            $storage['extension'] = $extension;
            $storage['mime_type'] = $mimeType;
            $storage['width'] = $imageInfo['width'];
            $storage['height'] = $imageInfo['height'];
            if (empty($storage['path'])) {
                $storage['path'] = '/uploads/' . $file['name'];
            }
            return $result;
        } catch (Exception $e) {
            $this->log($e->getMessage());
            return false;
        }
    }

    public function onBeforeDelete($Event) {
        if (!$this->_check($Event)) {
            return true;
        }
        $model = $Event->subject();
        $entity = $Event->data('record');
        $fields = array('adapter', 'filename');
        $asset = $model->findById($entity->id, $fields)->first();
        $adapter = StorageManager::adapter($asset['adapter']);
        if ($adapter->has('filename')) {
            $adapter->delete($asset->filename);
        }
        return $model->deleteAll(array('parent_asset_id' => $entity->id), true, true);
    }

    protected function _parentAsset($attachment) {
        $path = $attachment->import_path;
        $parts = pathinfo($path);
        if (strpos($parts['filename'], '.') === false) {
            // old style, no resize indicator, dimension prepended
            list($size, $filename) = explode('_', $parts['filename'], 2);
        } else {
            // new style, with resize indicator appended before extension
            $filename = substr($parts['filename'], 0, strrpos($parts['filename'], '.'));
        }

        // strip cacheDir if found
        $dirname = $parts['dirname'];
        $pos = strpos($parts['dirname'], '/', 1);
        if ($pos !== false) {
            $dirname = substr($parts['dirname'], 0, $pos);
        }

        $filename = rtrim(WWW_ROOT, '/') . $dirname . '/' . $filename . '.' . $parts['extension'];
        if (file_exists($filename)) {
            $hash = sha1_file($filename);
            return $this->Attachments->Assets->findByHash($hash)->first();
        }
        return false;
    }

}
