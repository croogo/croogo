<?php

namespace Croogo\FileManager\Model\Table;

use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Log\LogTrait;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use Croogo\Nodes\Model\Table\NodesTable;

/**
 * Attachment Model
 *
 * @category FileManager.Model
 * @package  Croogo.FileManager.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AttachmentsTable extends NodesTable
{
    use LogTrait;

/**
 * type
 */
    public $type = 'attachment';

/**
 * Uploads directory
 *
 * relative to the webroot.
 *
 * @var string
 * @access public
 */
    public $uploadsDir = 'uploads';

    public function initialize(array $config)
    {
        $this->table('nodes');
        $this->addBehavior('Croogo/Core.Tree', [
            'scope' => [
                'type' => $this->type,
            ],
        ]);
        parent::initialize($config);
    }

    /**
 * Save uploaded file
 *
 * @param array $entity data as POSTed from form
 * @return array|boolean false for errors or array containing fields to save
 */
    protected function _saveUploadedFile($entity)
    {
        $file = $entity->file;
        $dir = WWW_ROOT . $this->uploadsDir;

        // Check if dir exists
        if (!file_exists($dir)) {

            // Check if debug is enabled, to be consistent on only creating
            // folders when debug is enabled across the whole framework.
            if (Configure::read('debug')) {
                mkdir($dir);
            } else {
                return false;
            }
        }

        // check if file with same path exists
        $destination = $dir . DS . $file['name'];
        if (file_exists($destination)) {
            $newFileName = Text::uuid() . '-' . $file['name'];
            $destination = $dir . DS . $newFileName;
        } else {
            $newFileName = $file['name'];
        }

        // remove the extension for title
        if (explode('.', $file['name']) > 0) {
            $fileTitleE = explode('.', $file['name']);
            array_pop($fileTitleE);
            $fileTitle = implode('.', $fileTitleE);
        } else {
            $fileTitle = $file['name'];
        }

        $entity->title = $fileTitle;
        $entity->slug = $newFileName;
        $entity->body = '';
        $entity->mime_type = $file['type'];
        $entity->type = $this->type;
        $entity->path = '/' . $this->uploadsDir . '/' . $newFileName;
        // move the file
        $moved = move_uploaded_file($file['tmp_name'], $destination);
        if ($moved) {
            return $entity;
        }

        return false;
    }

/**
 * Saves model data
 *
 * @see Model::save()
 */
    public function save(EntityInterface $entity, $options = [])
    {
        $data = true;
        if (isset($entity->file['tmp_name'])) {
            $data = $this->_saveUploadedFile($entity);
        }
        if (!$data) {
            return $entity->errors(['file' => __d('croogo', 'Error during file upload')]);
        }
        return parent::save($entity, $options);
    }

/**
 * Removes record for given ID.
 *
 * @see Model::delete()
 */
    public function delete(EntityInterface $data, $options = [])
    {
        $attachment = $this->find()
            ->where([
                'id' => $data->id,
                'type' => $this->type,
            ])
            ->first();

        $filename = $attachment->slug;
        $uploadsDir = WWW_ROOT . $this->uploadsDir . DS;
        $fullpath = $uploadsDir . DS . $filename;
        if (file_exists($fullpath)) {
            $result = unlink($fullpath);
            if ($result) {
                $info = pathinfo($filename);
                array_map('unlink', glob(
                    $uploadsDir . DS . 'resized' . DS . $info['filename'] . '.resized-*.' . $info['extension']
                ));
                return parent::delete($attachment, $options);
            } else {
                return false;
            }
        } else {
            return parent::delete($attachment, $options);
        }
    }
}
