<?php

namespace Croogo\FileManager\Event;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\LogTrait;
use Croogo\FileManager\Utility\FileStorageUtils;
use Croogo\FileManager\Utility\StorageManager;
use Exception;

class LocalAttachmentStorageHandler extends BaseStorageHandler implements EventListenerInterface
{

    use LogTrait;

    public function implementedEvents()
    {
        return [
            'FileStorage.beforeSave' => 'onBeforeSave',
            'FileStorage.beforeDelete' => 'onBeforeDelete',
            'Assets.AssetsImageHelper.resize' => 'onResizeImage',
        ];
    }

    public function onBeforeSave(Event $event)
    {
        if (!$this->_check($event)) {
            return true;
        }
        $model = $event->getSubject();

        $storage = $event->getData('record');

        if (empty($storage->file)) {
            if (isset($storage->path) && empty($storage->filename)) {
                $path = rtrim(WWW_ROOT, '/') . $storage->path;
                $imageInfo = $this->__getImageInfo($path);

                $fp = fopen($path, 'r');
                $stat = fstat($fp);
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
        $filesystem = StorageManager::adapter($storage->adapter);
        try {
            if (!file_exists($file['tmp_name'])) {
                throw new Exception($this->Attachments->Assets->checkFileUpload($storage));
            }
            $raw = file_get_contents($file['tmp_name']);
            $key = sha1($raw);
            $extension = strtolower(FileStorageUtils::fileExtension($file['name']));

            $imageInfo = $this->__getImageInfo($file['tmp_name']);
            if (isset($imageInfo['mimeType'])) {
                $mimeType = $imageInfo['mimeType'];
            } else {
                $mimeType = $file['type'];
            }

            $prefix = null;
            if (empty($storage['path'])) {
                $prefix = FileStorageUtils::trimPath(FileStorageUtils::randomPath($file['name']));
            }
            $fullpath = $prefix . '/' . $key . '.' . $extension;
            $result = $filesystem->write($fullpath, $raw);
            $storage['path'] = '/assets/' . $fullpath;
            $storage['filename'] = $file['name'];
            $storage['filesize'] = $file['size'];
            $storage['hash'] = sha1($raw);
            $storage['mime_type'] = $mimeType;
            $storage['width'] = $imageInfo['width'];
            $storage['height'] = $imageInfo['height'];
            $storage['extension'] = $extension;

            return $result;
        } catch (Exception $e) {
            $event->getData('record')->setErrors(['path' => $e->getMessage()]);
            $this->log($e->getMessage());

            return false;
        }
    }

    public function onBeforeDelete($event)
    {
        $model = $event->getSubject();
        if (!$this->_check($event)) {
            return true;
        }

        $entity = $event->getData('record');

        $model = $event->getSubject();
        $fields = ['adapter', 'path'];
        $data = $model->get($entity->id, compact('fields'));

        $filesystem = StorageManager::adapter($data->adapter);
        $key = str_replace('/assets', '', $data->path);
        if ($filesystem->has($key)) {
            $filesystem->delete($key);
        }

        $forDeletions = $model->find()
            ->select(['id', 'adapter', 'path'])
            ->where(['parent_asset_id' => $entity->id])
            ->toArray();
        foreach ($forDeletions as $toDelete) {
            $model->delete($toDelete);
        }

        return true;
    }

    /**
     * Find parent of the resized image
     */
    protected function _parentAsset($attachment)
    {
        $path = $attachment->import_path;
        $parts = pathinfo($path);
        list($filename, ) = explode('.', $parts['filename'], 2);
        $filename = rtrim(WWW_ROOT, '/') . $parts['dirname'] . '/' . $filename . '.' . $parts['extension'];
        if (file_exists($filename)) {
            $hash = sha1_file($filename);

            return $this->Attachments->Assets->findByHash($hash)->first();
        }

        return false;
    }
}
