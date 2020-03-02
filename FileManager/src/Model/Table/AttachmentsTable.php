<?php

namespace Croogo\FileManager\Model\Table;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Filesystem\Folder;
use Cake\Log\LogTrait;
use Cake\ORM\Query;
use Cake\Utility\Hash;
use Char0n\FFMpegPHP\Movie;
use Croogo\Core\Model\Table\CroogoTable;
use Croogo\FileManager\Utility\StorageManager;
use Exception;
use finfo;
use Intervention\Image\ImageManagerStatic as Image;
use InvalidArgumentException;
use RuntimeException;

/**
 * Attachments Model
 *
 */
class AttachmentsTable extends CroogoTable
{

    use LogTrait;

    /**
     * @var array
     */
    public $findMethods = [
        'duplicate' => true,
        'modelAttachments' => true,
        'versions' => true,
    ];

    /**
     * @param array $config
     * @return void
     */
    public function initialize(array $config)
    {
        $this->setTable('attachments');

        $this->hasOne('Assets', [
            'className' => 'Croogo/FileManager.Assets',
            'foreignKey' => 'foreign_key',
            'dependent' => true,
            'cascadeCallbacks' => true,
            'conditions' => [
                'parent_asset_id IS' => null,
                'model' => 'Attachments',
            ],
        ]);

        $this->addBehavior('Timestamp');
        $this->addBehavior('Croogo/Core.Trackable');
        $this->addBehavior('Search.Search');
        //$this->addBehavior('Burzum/Imagine.Imagine');

        $this->searchManager()
            ->add('title', 'Search.Like', [
                'field' => $this->Assets->aliasField('filename'),
                'before' => true,
                'after' => true,
            ])
            ->add('search', 'Search.Callback', [
                'callback' => [$this, 'filterAttachments'],
            ])
            ->add('filename', 'Search.Like', [
                'field' => $this->Assets->aliasField('filename'),
                'before' => true,
                'after' => true,
            ])
            ->value('model', [
                'field' => $this->Assets->AssetUsages->aliasField('model'),
            ])
            ->value('foreign_key', [
                'field' => $this->Assets->AssetUsages->aliasField('foreign_key'),
            ])
            ->value('asset_id', [
                'field' => $this->Assets->aliasField('id'),
            ])
            ->value('id', [
                'field' => $this->aliasField('id'),
            ])
            ->value('type', [
                'field' => $this->Assets->AssetUsages->aliasField('type'),
            ]);
    }

    /**
     * @param $query
     * @param $args
     * @param $filter
     *
     * @return mixed
     */
    public function filterAttachments($query, $args, $filter)
    {
        $conditions = [];
        if (!empty($args['search'])) {
            $filter = '%' . $args['search'] . '%';
            $conditions = [
                'OR' => [
                    $this->aliasField('title') . ' LIKE' => $filter,
                    $this->aliasField('excerpt') . ' LIKE' => $filter,
                    $this->aliasField('body') . ' LIKE' => $filter,
                ],
            ];
            $query
                ->contain('Assets')
                ->orWhere($conditions);
        }

        return $query;
    }

    /**
     * Find duplicates based on hash
     */
    public function findDuplicate(Query $query, array $options)
    {
        if (empty($options['hash'])) {
            return $query;
        }
        $hash = $options['hash'];
        $query->where([
            $this->aliasField('hash') => $hash,
        ]);

        return $query;
    }

    /**
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findModelAttachments(Query $query, array $options)
    {
        $model = $foreignKey = null;
        if (isset($options['model'])) {
            $model = $options['model'];
            unset($options['model']);
        }
        if (isset($options['foreign_key'])) {
            $foreignKey = $options['foreign_key'];
            unset($options['foreign_key']);
        }
        $assetsJoinConditions = [
            $this->Assets->aliasField('model') . ' = \'Attachments\'',
            $this->Assets->aliasField('foreign_key') . ' = ' . $this->aliasField('id'),
        ];
        $assetUsagesJoinConditions = [
            $this->Assets->aliasField('id') . ' = ' . $this->Assets->AssetUsages->aliasField('asset_id'),
        ];
        $this->associations()->remove('Assets');
        $this->addAssociations([
            'hasOne' => [
                'Assets' => [
                    'className' => 'Croogo/FileManager.Assets',
                    'foreignKey' => false,
                    'conditions' => $assetsJoinConditions,
                ],
                'AssetUsages' => [
                    'className' => 'Croogo/FileManager.AssetUsages',
                    'foreignKey' => false,
                    'conditions' => $assetUsagesJoinConditions,
                ],
            ]
        ]);
        $query->contain('Assets');
        $query->contain('AssetUsages');

        if (isset($model) && isset($foreignKey)) {
            $query->where([
                $this->Assets->AssetUsages->aliasField('model') => $model,
                $this->Assets->AssetUsages->aliasField('foreign_key') => $foreignKey,
            ]);
        }

        return $query;
    }

    /**
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findVersions(Query $query, array $options)
    {
        $assetId = $model = $foreignKey = null;
        if (isset($options['asset_id'])) {
            $assetId = $options['asset_id'];
            unset($options['asset_id']);
        }
        if (isset($options['search']['asset_id'])) {
            $assetId = $options['search']['asset_id'];
            unset($options['search']['asset_id']);
        }
        if (isset($options['model'])) {
            $model = $options['model'];
            unset($options['model']);
        }
        if (isset($options['foreign_key'])) {
            $foreignKey = $options['foreign_key'];
            unset($options['foreign_key']);
        }
        if (isset($options['all'])) {
            $all = $options['all'];
            unset($options['all']);
        }
        $assetsJoinConditions = [
            $this->Assets->aliasField('model') . ' = \'Attachments\'',
            $this->Assets->aliasField('foreign_key') . ' = ' . $this->aliasField('id'),
        ];
        $assetUsagesJoinConditions = [
            $this->Assets->aliasField('id') . ' = ' . $this->Assets->AssetUsages->aliasField('asset_id'),
        ];
        $this->associations()->remove('Assets');
        $this->addAssociations([
            'hasOne' => [
                'Assets' => [
                    'className' => 'Croogo/FileManager.Assets',
                    'foreignKey' => false,
                    'dependent' => true,
                    'conditions' => $assetsJoinConditions,
                ],
                'AssetUsages' => [
                    'className' => 'Croogo/FileManager.AssetUsages',
                    'foreignKey' => false,
                    'conditions' => $assetUsagesJoinConditions,
                ],
            ]
        ]);

        $query->contain('Assets');

        if ($assetId && !isset($all)) {
            $conditions = [
                'OR' => [
                    $this->Assets->aliasField('id') => $assetId,
                    $this->Assets->aliasField('parent_asset_id') => $assetId,
                ],
            ];
            $query->orWhere($conditions);
        }

        return $query;
    }

    /**
     * @param Event $event
     * @param EntityInterface $entity
     * @param ArrayObject|null $options
     *
     * @return bool|string
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options = null)
    {
        if (!empty($entity->asset->file['name'])) {
            $file = $entity->asset->file;
            $attachment = $entity;
            if (empty($attachment->title)) {
                $attachment->title = $file['name'];
            }
            if (empty($attachment->slug)) {
                $attachment->slug = $file['name'];
            }
            if (empty($attachment->hash)) {
                if (empty($file['tmp_name'])) {
                    return 'Uploaded file is empty';
                } else {
                    $attachment->hash = sha1_file($file['tmp_name']);
                }
            }
        }

        return true;
    }

    /**
     * Create an Attachments data from $file
     *
     * @param $file string Path to file
     * @return array|string Array of data or error message
     * @throws InvalidArgumentException
     */
    public function createFromFile($file)
    {
        if (!file_exists($file)) {
            throw new InvalidArgumentException(__('Attachments::createFromFile(): {0} cannot be found', $file));
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $fp = fopen($file, 'r');
        $stat = fstat($fp);
        fclose($fp);
        $hash = sha1_file($file);
        $duplicate = isset($hash) ?
            $this->find('duplicate', ['hash' => $hash])->toArray() :
            false;
        if ($duplicate) {
            $firstDupe = $duplicate[0]->id;

            return sprintf('%s is duplicate to asset: %s', str_replace(APP, '', $file), $firstDupe);
        }
        $path = str_replace(rtrim(WWW_ROOT, '/'), '', $file);

        $sizeDelimiterPos = strrpos($path, '-');
        $originalPath = substr($path, 0, $sizeDelimiterPos);
        $extDelimiterPos = strrpos($path, '.');
        $originalExt = substr($path, $extDelimiterPos);
        $originalFile = $originalPath . $originalExt;

        if (file_exists(WWW_ROOT . $originalFile)) {
            // Found a possible parent file. Retrieve its parent and store
            // the file path in a temp property asset_path so it gets picked up
            // by _createImportTask() and runTask()
            $attachment = $this->find()
                ->contain('Assets')
                ->where([
                    'import_path' => $originalFile,
                ])
                ->first();
            if ($attachment) {
                $attachment->asset_path = $path;
                $attachment->setDirty('asset_path', false);

                return $attachment;
            }
        }

        $attachment = $this->newEntity([
            'path' => $path,
            'import_path' => $path,
            'title' => basename($file),
            'slug' => basename($file),
            'mime_type' => $finfo->file($file),
            'hash' => $hash,
            'status' => true,
            'created' => date('Y-m-d H:i:s', $stat[9]),
            'modified' => date('Y-m-d H:i:s', time()),
        ]);

        return $attachment;
    }

    /**
     * Create Import task
     */
    protected function _createImportTask($files, $options)
    {
        $data = [];
        $copy = [];
        $error = [];
        foreach ($files as $file) {
            $attachment = $this->createFromFile($file);
            if ($attachment instanceof EntityInterface) {
                $data[] = $attachment;
                $copy[] = ['from' => $attachment->asset_path ?: $attachment->import_path];
                $error[] = null;
            } else {
                $data[] = null;
                $copy[] = null;
                $error[] = $attachment;
            }
        }

        return compact('data', 'copy', 'error');
    }

    /**
     * Perform the actual import based on $task
     *
     * @param $task array Array of tasks
     */
    public function runTask($task)
    {
        $imports = $errors = 0;
        foreach ($task['copy'] as $i => $source) {
            if (!$source) {
                continue;
            }

            $file = WWW_ROOT . $source['from'];

            $fp = fopen($file, 'r');
            $stat = fstat($fp);
            fclose($fp);

            $pathinfo = pathinfo($file);

            $width = $height = null;
            if (strcmp($task['data'][$i]->mime_type, 'image') !== false) {
                $sizeinfo = getimagesize($file);
                $width = $sizeinfo[0];
                $height = $sizeinfo[1];
            }

            $originalAsset = $task['data'][$i]->asset;
            if ($originalAsset) {
                $asset = $this->Assets->newEntity([
                    'parent_asset_id' => $originalAsset->id,
                    'model' => 'Attachments',
                    'foreign_key' => $originalAsset->foreign_key,
                    'adapter' => 'LegacyLocalAttachment',
                    'filename' => basename($file),
                    'filesize' => $stat['size'],
                    'width' => $width,
                    'height' => $height,
                    'hash' => sha1_file($file),
                    'extension' => $pathinfo['extension'],
                    'mime_type' => $originalAsset->mime_type,
                    'path' => $source['from'],
                ]);
                $result = $this->Assets->save($asset, ['atomic' => true]);
            } else {
                $task['data'][$i]->asset = $this->Assets->newEntity([
                    'model' => 'Attachments',
                    'adapter' => 'LegacyLocalAttachment',
                    'filename' => basename($file),
                    'filesize' => $stat['size'],
                    'width' => $width,
                    'height' => $height,
                    'hash' => $task['data'][$i]->hash,
                    'extension' => $pathinfo['extension'],
                    'mime_type' => $task['data'][$i]->mime_type,
                    'path' => $source['from'],
                ]);
                $result = $this->save($task['data'][$i], ['atomic' => true]);
            }
            if ($result) {
                $imports++;
            } else {
                $errors++;
            }
        }

        return compact('imports', 'errors');
    }

    /**
     * Import files into the assets repository
     *
     * @param $dir array|string Path to import
     * @param $regex string Regex to filter files to import
     * @param $options array
     * @throws InvalidArgumentException
     */
    public function importTask($dirs = [], $regex = '.*', $options = [])
    {
        $options = Hash::merge([
            'recursive' => true,
        ], $options);
        foreach ($dirs as $dir) {
            if (substr($dir, -1) === '/') {
                $dir = substr($dir, 0, strlen($dir) - 1);
            }
            if (!is_dir($dir)) {
                throw new InvalidArgumentException(__('{0} is not a directory', $dir));
            }
            $folder = new Folder($dir, false, false);
            if ($options['recursive']) {
                $files = $folder->findRecursive($regex, false);
            } else {
                $files = $folder->find($regex, false);
                $files = array_map(
                    function ($v) use ($dir) {
                        return APP . $dir . '/' . $v;
                    },
                    $files
                );
            }
        }

        if (empty($files)) {
            throw new Exception('importTask: cannot detect files to import');
        }

        return $this->_createImportTask($files, $options);
    }

    /**
     * Create a video thumbnail
     *
     * @param int $id Attachment Id
     * @param int $w New Width
     * @param int $h New Height
     * @param array $options Options array
     */
    public function createVideoThumbnail($id, $w = null, $h = null, $options = [])
    {
        if (!class_exists('Char0n\FFMpegPHP\Movie')) {
            throw new RunTimeException('Char0n\FFMpegPHP\Movie class not found');
        }
        $this->recursive = -1;

        $attachment = $this->get($id, [
            'contain' => ['Assets'],
        ]);
        $asset =& $attachment->asset;
        $path = rtrim(WWW_ROOT, '/') . $asset->path;

        $info = pathinfo($asset->path);
        $ind = sprintf('.resized-%dx%d.', $w, $h);

        $filename = $info['filename'] . $ind . 'jpg';
        $thumbnailPath = $info['dirname'] . DS . $filename;
        $writePath = rtrim(WWW_ROOT, '/') . $thumbnailPath;

        $ffmpeg = new Movie($path, null);
        $frameCount = $ffmpeg->getFrameCount();
        $width = $ffmpeg->getFrameWidth();
        $height = $ffmpeg->getFrameHeight();
        $posterFrameIndex = intval($frameCount / 4);
        $frame = $ffmpeg->getFrame($posterFrameIndex, $w, $h);
        imagejpeg($frame->toGDImage(), $writePath, 100);

        $fp = fopen($writePath, 'r');
        $stat = fstat($fp);
        fclose($fp);

        $adapter = $asset['adapter'];

        $entity = $this->Assets->newEntity([
            'filename' => $filename,
            'path' => dirname($asset->path) . '/' . $filename,
            'model' => $asset->model,
            'extension' => 'jpg',
            'parent_asset_id' => $asset->id,
            'foreign_key' => $asset->foreign_key,
            'adapter' => $adapter,
            'mime_type' => 'image/jpeg',
            'width' => $width,
            'height' => $height,
            'filesize' => $stat[7],
        ]);

        $asset = $this->Assets->save($entity);

        return $asset;
    }

    /**
     * Copy an existing attachment and resize with width: $w and height: $h
     *
     * @param int $id Attachment Id
     * @param int $w New Width
     * @param int $h New Height
     * @param array $options Options array
     */
    public function createResized($id, $w, $h, $options = [])
    {
        $options = Hash::merge([
            'uploadsDir' => 'assets',
        ], $options);
        $attachment = $this->get($id, [
            'contain' => ['Assets'],
        ]);
        $asset = $attachment->asset;
        $path = rtrim(WWW_ROOT, '/') . $asset->path;

        $image = Image::make($path);

        $stream = $image
            ->resize($w, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->stream();

        $newWidth = $image->width();
        $newHeight = $image->height();

        $info = pathinfo($asset->path);
        $ind = sprintf('.resized-%dx%d.', $newWidth, $newHeight);

        $uploadsDir = str_replace('/' . $options['uploadsDir'] . '/', '', dirname($asset['path'])) . '/';
        $filename = $info['filename'] . $ind . $info['extension'];
        $writePath = $uploadsDir . $filename;

        $adapter = $asset->adapter;

        $filesystem = StorageManager::adapter($adapter);
        $filesystem->write($writePath, $stream);

        $entity = $this->Assets->newEntity([
            'filename' => $filename,
            'path' => dirname($asset->path) . '/' . $filename,
            'model' => $asset->model,
            'extension' => $asset->extension,
            'parent_asset_id' => $asset->id,
            'foreign_key' => $asset->foreign_key,
            'adapter' => $adapter,
            'mime_type' => $asset->mime_type,
            'width' => $newWidth,
            'height' => $newHeight,
            'filesize' => $image->filesize(),
            'hash' => sha1($stream),
        ]);

        $asset = $this->Assets->save($entity);

        return $asset;
    }
}
