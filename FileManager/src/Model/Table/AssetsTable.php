<?php

namespace Croogo\FileManager\Model\Table;

use ArrayObject;
use Cake\Event\Event;
use Cake\Datasource\EntityInterface;
use Cake\Validation\Validator;
use Croogo\Core\Croogo;
use Croogo\Core\Model\Table\CroogoTable;

class AssetsTable extends CroogoTable {

    public $validate = array(
        'file' => 'checkFileUpload'
    );

    public function initialize(array $config) {
        parent::initialize($config);

        $this->setTable('assets');

        $this->hasMany('AssetUsages', [
            'className' => 'Croogo/FileManager.AssetUsages',
            'dependent' => true,
        ]);

        $this->belongsTo('Attachments', [
            'className' => 'Croogo/FileManager.Attachments',
            'foreignKey' => 'foreign_key',
            'conditions' => [
                'Assets.model' => 'Attachments',
            ],
            'counterCache' => 'asset_count',
            'counterScope' => [
                'Assets.model' => 'Attachments',
            ],
        ]);

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');
        $this->addBehavior('Croogo/Core.Trackable');

    }

    public function validationDefault(Validator $validator) {
        $validator
            ->requirePresence('adapter', 'create');
        return $validator;
    }

    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options = null) {
        $adapter = $entity->get('adapter');
        if (!$entity->filename) {
            $entity->filename = '';
        }
        if (!$entity->path) {
            $entity->path = '';
        }
        $Event = Croogo::dispatchEvent('FileStorage.beforeSave', $this, array(
            'record' => $entity,
            'adapter' => $adapter,
        ));
        if ($Event->isStopped()) {
            return false;
        }
        return true;
    }

    public function beforeDelete(Event $event, EntityInterface $entity, ArrayObject $options = null) {
        $Event = Croogo::dispatchEvent('FileStorage.beforeDelete', $this, array(
            'record' => $entity,
        ));
        if ($Event->isStopped()) {
            return false;
        }
        return true;
    }

    public function checkFileUpload($check) {
        switch($check['file']['error']){
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            break;
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            break;
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded.';
            break;
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded.';
            break;
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder.';
            break;
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk.';
            break;
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload.';
            break;
            case UPLOAD_ERR_OK:
                return true;
            break;
        }
    }

}
