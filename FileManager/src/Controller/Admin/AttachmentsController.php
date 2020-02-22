<?php

namespace Croogo\FileManager\Controller\Admin;

use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Log\Log;
use Cake\Utility\Hash;
use Croogo\Core\Croogo;
use Exception;

/**
 * Attachments Controller
 *
 * This file will take care of file uploads (with rich text editor integration).
 *
 * @category Assets.Controller
 * @package  Assets.Controller
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @author   Rachman Chavik <contact@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AttachmentsController extends AppController
{

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    public $helpers = [
        'Croogo/FileManager.AssetsImage',
        'Croogo/FileManager.FileManager',
        'Text',
    ];

    public $paginate = [
        'paramType' => 'querystring',
        'limit' => 5,
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Search.Prg', [
            'actions' => [
                'index', 'browse', 'listings',
            ],
        ]);

        $this->_loadCroogoComponents(['BulkProcess']);
        $this->loadModel('Croogo/FileManager.Attachments');
    }

    /**
     * Before executing controller actions
     *
     * @return void
     * @access public
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        if ($this->getRequest()->getParam('action') == 'resize') {
            $this->Security->setConfig('validatePost', false);
        }
    }

    /**
     * Admin index
     *
     * @return void
     * @access public
     */
    public function index()
    {
        $this->set('title_for_layout', __d('croogo', 'Attachments'));

        $this->set('searchFields', [
            'search',
            'model' => [
                'type' => 'hidden',
            ],
            'foreign_key' => [
                'type' => 'hidden',
            ],
            'all' => [
                'type' => 'hidden',
            ],
        ]);

        $query = $this->Attachments->find();

        $isChooser = false;

        if ($this->getRequest()->getQuery('links') || $this->getRequest()->getQuery('chooser')) {
            $isChooser = true;
        }

        $model = $this->getRequest()->getQuery('model');
        $foreignKey = $this->getRequest()->getQuery('foreign_key');
        $this->set(compact('model', 'foreignKey'));
        $httpQuery = (array)$this->getRequest()->getQuery();

        if ($this->getRequest()->getQuery('manage')) {
            $finder = 'versions';
            unset($httpQuery['model']);
            unset($httpQuery['foreign_key']);
        } elseif (isset($httpQuery['asset_id']) ||
            isset($httpQuery['all'])
        ) {
            $finder = 'versions';
            unset($httpQuery['model']);
            unset($httpQuery['foreign_key']);

            if (!$this->getRequest()->getQuery('sort')) {
                $query->order([
                    $this->Attachments->aliasField('id') => 'desc',
                ]);
            }
        } elseif ($this->getRequest()->getQuery('search')) {
            $finder = null;
        } else {
            if (empty($model) || empty($foreignKey)) {
                $finder = 'versions';
            } else {
                $finder = 'modelAttachments';
            }
            $query->where([
                'Assets.parent_asset_id IS' => null,
            ]);
        }

        if (!$this->getRequest()->getQuery('sort')) {
            $query->order(['Attachments.created' => 'DESC']);
        }

        if ($isChooser) {
            if ($this->getRequest()->getQuery('chooser_type') == 'image') {
                $query->where([
                    'Assets.mime_type LIKE' => 'image/%',
                ]);
            } else {
                $query->where([
                    'Assets.mime_type NOT LIKE' => 'image/%',
                ]);
            }
        }

        $query->find('search', [
            'search' => $httpQuery,
        ]);

        if (isset($finder)) {
            $query->find($finder);
        }

        $this->set('attachments', $this->paginate($query));

        if ($this->getRequest()->getQuery('links') || $this->getRequest()->getQuery('chooser')) {
            $this->viewBuilder()->setLayout('admin_popup');
            $this->render('chooser');
        }
    }

    /**
     * Admin add
     *
     * @return void
     * @access public
     */
    public function add()
    {
        $this->set('title_for_layout', __d('croogo', 'Add Attachment'));

        if ($this->getRequest()->getQuery('editor')) {
            $this->viewBuilder()->setLayout('admin_popup');
        }

        if ($this->getRequest()->is('post')) {
            $data = $this->getRequest()->getData();
            if (!empty($data)) {
                $entity = $this->Attachments->newEntity($data);
                $errors = $entity->getErrors();
            } else {
                $errors = [
                    'file' => __d('croogo', 'Upload failed. Please ensure size does not exceed the server limit.')
                ];
            }

            if (empty($errors)) {
                $attachment = $this->Attachments->save($entity);

                $errors = $entity->getErrors();
                if (empty($errors) && $attachment) {
                    $eventKey = 'Controller.FileManager/Attachment.newAttachment';
                    Croogo::dispatchEvent($eventKey, $this, compact('attachment'));
                } else {
                    Log::error('Failed saving attachments:');
                    Log::error(print_r($errors, true));
                }
            } else {
                Log::error('Failed validating attachments:');
                Log::error(print_r($errors, true));
            }

            if ($this->getRequest()->is('ajax')) {
                $files = [];
                $error = false;

                if (empty($errors)) {
                    $this->viewBuilder()->setClassName('Json');
                    $files = [[
                        'url' => $attachment->asset->path,
                        'thumbnail_url' => $attachment->asset->path,
                        'name' => $attachment->title,
                        'type' => $attachment->asset->mime_type,
                        'size' => $attachment->asset->filesize,
                    ]];
                } else {
                    $error = implode("\n", Hash::flatten($errors));
                    $files = [[
                        'error' => $error,
                    ]];
                }

                $this->set(compact('files', 'error'));
                $this->set('_serialize', ['files', 'error']);

                return;
            } else {
                // noop
            }

            if ($attachment) {
                $this->Flash->success(__d('croogo', 'The Attachment has been saved'));
                $url = [];
                if (isset($saved->asset->asset_usage[0])) {
                    $usage = $saved->asset->asset_usage[0];
                    if (!empty($usage->model) && !empty($usage->foreign_key)) {
                        $url['?']['model'] = $usage->model;
                        $url['?']['foreign_key'] = $usage->foreign_key;
                    }
                }
                if ($this->getRequest()->getQuery('editor')) {
                    $url = array_merge($url, ['action' => 'browse']);
                } else {
                    $url = array_merge($url, ['action' => 'index']);
                }

                return $this->redirect($url);
            } else {
                $this->Flash->error(__d('croogo', 'The Attachment could not be saved. Please, try again.'));
            }
        } else {
            // noop
        }

        $attachment = $this->Attachments->newEntity();
        $this->set(compact('attachment'));
    }

    /**
     * Admin edit
     *
     * @param int $id
     * @return \Cake\Http\Response|void
     * @access public
     */
    public function edit($id = null)
    {
        $this->set('title_for_layout', __d('croogo', 'Edit Attachment'));

        if ($this->getRequest()->getQuery('editor')) {
            $this->layout = 'admin_popup';
        }

        $redirect = ['action' => 'index'];
        if (!empty($this->getRequest()->getQuery())) {
            $redirect = array_merge(
                $redirect,
                ['action' => 'browse', '?' => $this->getRequest()->getQuery()]
            );
        }

        if (!$id && empty($this->getRequest()->getData())) {
            $this->Flash->error(__d('croogo', 'Invalid Attachment'));

            return $this->redirect($redirect);
        }
        $attachment = $this->Attachments->get($id, [
            'contain' => [
                'Assets',
            ],
        ]);
        if (!empty($this->getRequest()->getData())) {
            $attachment = $this->Attachments->patchEntity($attachment, $this->getRequest()->getData());
            if ($this->Attachments->save($attachment)) {
                $this->Flash->success(__d('croogo', 'The Attachment has been saved'));

                $redirect = $this->getRequest()->getQuery('redirect') ?: [
                    'action' => 'index',
                ];
                return $this->redirect($redirect);
            } else {
                $this->Flash->error(__d('croogo', 'The Attachment could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('attachment'));
    }

    /**
     * Admin delete
     *
     * @param int $id
     * @return \Cake\Http\Response|void
     * @access public
     */
    public function delete($id = null)
    {
        if (!$id) {
            $this->Flash->error(__d('croogo', 'Invalid id for Attachment'));

            return $this->redirect(['action' => 'index']);
        }

        $redirect = $this->referer(['action' => 'index'], true);

        $attachment = $this->Attachments->get($id);
        $this->Attachments->getConnection()->begin();
        if ($this->Attachments->delete($attachment)) {
            $this->Attachments->getConnection()->commit();
            $this->Flash->success(__d('croogo', 'Attachment deleted'));

            return $this->redirect($redirect);
        }

        $this->Flash->error(__d('croogo', 'Invalid id for Attachment'));

        return $this->redirect($redirect);
    }

    /**
     * Admin browse
     *
     * @return void
     * @access public
     */
    public function browse()
    {
        $this->viewBuilder()->setLayout('admin_popup');
        $this->index();
    }

    public function listing()
    {
        if ($this->getRequest()->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
            $this->paginate['limit'] = 100;
        }

        $query = $this->Attachments
            ->find('search', [
                'search' => (array)$this->getRequest()->getQuery(),
            ])
            ->find('modelAttachments');
        $attachments = $this->paginate($query);
        $this->set(compact('attachments'));
    }

    public function resize($id = null)
    {
        if (empty($id)) {
            throw new NotFoundException('Missing Asset Id to resize');
        }

        $result = false;
        if (!empty($this->getRequest()->getData('width'))) {
            $width = $this->getRequest()->getData('width');
            try {
                $result = $this->Attachments->createResized($id, $width, null);
            } catch (Exception $e) {
                $result = $e->getMessage();
            }
        }

        $this->set(compact('result'));
        $this->set('_serialize', 'result');
    }

    public function process()
    {
        $Attachments = $this->Attachments;
        list($action, $ids) = $this->BulkProcess->getRequestVars($Attachments->alias());

        $messageMap = [
            'delete' => __d('croogo', 'Attachments deleted'),
        ];

        return $this->BulkProcess->process($Attachments, $action, $ids, [
            'messageMap' => $messageMap,
        ]);
    }
}
