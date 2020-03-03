<?php

namespace Croogo\FileManager\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

/**
 * Class LinkedAssetsBehavior
 */
class LinkedAssetsBehavior extends Behavior
{

    /**
     * @var array
     */
    protected $_defaultConfig = [
        'key' => 'linked_assets',
    ];

    /**
     * @param array $config
     * @return void
     */
    public function initialize(array $config = [])
    {
        $this->_table->addAssociations([
            'hasMany' => [
                'AssetUsages' => [
                    'className' => 'Croogo/FileManager.AssetUsages',
                    'foreignKey' => 'foreign_key',
                    'dependent' => true,
                    'conditions' => [
                        'AssetUsages.model' => $this->_table->getAlias(),
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param Event $event
     * @param Query $query
     * @param ArrayObject $options
     * @param $primary
     *
     * @return Query
     */
    public function beforeFind(Event $event, Query $query, ArrayObject $options, $primary)
    {
        //if ($model->findQueryType == 'list') {
            //return $query;
        //}

        /*
        if (!isset($query['contain'])) {
            $contain = array();
            $relationCheck = array('belongsTo', 'hasMany', 'hasOne', 'hasAndBelongsToMany');
            foreach ($relationCheck as $relation) {
                if ($model->{$relation}) {
                    $contain = Hash::merge($contain, array_keys($model->{$relation}));
                }
            }
            if ($model->recursive >= 0 || $query['recursive'] >= 0 ) {
                $query = Hash::merge(array('contain' => $contain), $query);
            }
        }
        if (isset($query['contain'])) {
            if (!isset($query['contain']['AssetsAssetUsage'])) {
                $query['contain']['AssetsAssetUsage'] = 'AssetsAsset';
            }
        }
        */

        $query->contain('AssetUsages.Assets');
        $query->contain('AssetUsages.Assets.Attachments');

        $query->formatResults(function ($resultSet) {
            return $this->_formatResults($resultSet);
        });

        return $query;
    }

    /**
     * @param $results
     *
     * @return mixed
     */
    protected function _formatResults($results)
    {
        $key = $this->getConfig('key');

        if (isset($this->getTable()->Assets)) {
            $Assets = $this->getTable()->Assets;
        } else {
            $Assets = TableRegistry::getTableLocator()->get('Croogo/FileManager.Assets');
        }

        foreach ($results as $result) {
            if (!$result instanceof EntityInterface || !$result->has('asset_usages')) {
                continue;
            }
            $result->$key = [];
            foreach ($result->asset_usages as &$assetUsage) {
                if (!$assetUsage->has('asset')) {
                    continue;
                }

                if (empty($assetUsage->type)) {
                    $fields = [
                        'filename', 'filesize', 'width', 'height', 'mime_type',
                        'extension', 'hash', 'path', 'adapter',
                    ];
                    foreach ($fields as $field) {
                        $assetUsage->{$field} = $assetUsage->asset->{$field};
                    }
                    unset($assetUsage->asset);
                    $assetUsage->clean();
                    $result->{$key}['DefaultAsset'][] = $assetUsage;
                } else {
                    $seedId = isset($assetUsage->asset->parent_asset_id) ?
                        $assetUsage->asset->parent_asset_id :
                        $assetUsage->asset->id;
                    $relatedAssets = $Assets->find()
                        ->where([
                            'Assets.parent_asset_id' => $seedId,
                        ])
                        ->cache('linked_assets_' . $assetUsage->asset->id, 'nodes')
                        ->order(['width' => 'DESC']);
                    if (!$assetUsage->versions) {
                        $versions = [];
                    }
                    foreach ($relatedAssets as $related) {
                        $versions[] = $related;
                    }
                    $assetUsage->versions = $versions;

                    if (strstr($assetUsage->asset->mime_type, 'video') !== false && (
                        isset($versions[0]) &&
                        strstr($versions[0]->mime_type, 'image') !== false
                    )) {
                        $assetUsage->asset->poster_path = $versions[0]->path;
                    }

                    $result[$key][$assetUsage->type][] = $assetUsage->asset;

                //} else {
                    //$result[$key][$assetUsage->type][] = $assetUsage->asset;
                }
            }
            unset($result->asset_usages);
        }

        return $results;
    }

    /**
     * Import $path as $model's asset and automatically registers its usage record
     *
     * This method is intended for importing an existing file in the local
     * filesystem into Assets plugin with automatic usage record with the calling
     * model.
     *
     * Eg:
     *
     *   $Book = ClassRegistry::init('Book');
     *   $Book->Behaviors->load('Assets.LinkedAssets');
     *   $Book->importAsset('LocalAttachment', '/path/to/file');
     *
     * @param string $model Adapter name
     * @param string $adapter Path to file, relative from WWW_ROOT
     * @return bool
     */
    public function importAsset(Model $model, $adapter, $path, $options = [])
    {
        $options = Hash::merge([
            'usage' => [],
        ], $options);
        $Attachment = ClassRegistry::init('Assets.Attachments');
        $attachment = $Attachment->createFromFile(WWW_ROOT . $path);

        if (!is_array($attachment)) {
            return false;
        }

        $originalPath = WWW_ROOT . $path;
        $fp = fopen($originalPath, 'r');
        $stat = fstat($fp);
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $attachment['AssetsAsset'] = [
            'model' => $Attachment->alias,
            'adapter' => $adapter,
            'file' => [
                'name' => basename($originalPath),
                'tmp_name' => $originalPath,
                'type' => $finfo->file($originalPath),
                'size' => $stat['size'],
                'error' => UPLOAD_ERR_OK,
            ],
        ];
        $attachment = $Attachment->saveAll($attachment);

        $Attachment->AssetsAsset->recursive = -1;
        $asset = $Attachment->AssetsAsset->find('first', [
            'conditions' => [
                'model' => $Attachment->alias,
                'foreign_key' => $Attachment->id,
            ],
        ]);

        $Usage = $Attachment->AssetsAsset->AssetsAssetUsage;

        $usage = Hash::merge($options['usage'], [
            'asset_id' => $asset['AssetsAsset']['id'],
            'model' => $model->alias,
            'foreign_key' => $model->id,
        ]);
        $usage = $Usage->create($usage);

        $usage = $Usage->save($usage);
        if ($usage) {
            return true;
        }

        return false;
    }
}
